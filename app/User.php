<?php

namespace App;

use App\Traits\IsObservable;
use Kalnoy\Nestedset\NodeTrait;
use App\Jobs\{InviteUser, RequestOTP, VerifyOTP};
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Tightenco\Parental\ReturnsChildModels;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use ReturnsChildModels;
    use HasRoles;
    use IsObservable;
    use NodeTrait;
    
    public static $classes = [
        'admin'      => Admin::class,
        'operator'   => Operator::class,
        'staff'      => Staff::class,
        'subscriber' => Subscriber::class,
        'worker'     => Worker::class,
    ];

    protected $fillable = [
        'name', 'email', 'password', 'mobile'
    ];

    protected $dates = [
        'created_at', 'updated_at', 'verified_at'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $guard_name = 'web';
 
    public static function seed($code, $mobile, $parent)
    {
        if (! $model = static::withMobile($mobile)->first()) {
            $model = (static::$classes[$code])::create(compact('mobile'));
            if ($model->wasRecentlyCreated) {
                $model->appendToNode($parent);
                $model->save();
            }
        }
            
        return $model;
    }

    public function routeNotificationForNexmo($notification)
    {
        return $this->mobile;
    }

    public function routeNotificationForTwilio()
    {
        return $this->mobile;
    }

    public function routeNotificationForAuthy()
    {
        return $this->authy_id;
    }

    public function invite()
    {
        InviteUser::dispatch($this);

        return $this;
    }

    public function challenge()
    {
        RequestOTP::dispatch($this);
    }

    public function verify($otp)
    {
        VerifyOTP::dispatch($this, $otp);
    }

    public function isVerified()
    {
        return $this->verified_at && $this->verified_at <= now();
    }

    public function isVerificationStale()
    {
        return $this->verified_at && $this->verified_at->addSeconds(60) <= now();
    }

    public function verifiedBy($otp, $notSimulated = true)
    {
        $verified = ! $notSimulated || app('rinvex.authy.token')->verify($otp, $this->authy_id)->succeed();

        if ($verified) $this->forceFill(['verified_at' => now()])->save(); 

        return $this;
    }   

    public function generatePlacements()
    {
        $this->placements()->delete();

        foreach(User::$classes as $key => $values) {
            $code = Dictionary::generate(1,2,3);
            $type = $values;
            $message = 'Registered '.strtolower($key).' by '.$this->name;
            
            Placement::record(compact('code', 'type', 'message'), $this);   
        }

        return $this;
    }

    public function setMobileAttribute($value)
    {
        $this->attributes['mobile'] = Phone::number($value);
    }

    public function getNameAttribute($value)
    {
        return $value ? ucfirst($value) : $this->mobile;
    }

    public function messengers()
    {
        return $this->hasMany(Messenger::class);
    }

    public function placements()
    {
        return $this->hasMany(Placement::class);
    }

    public function scopeWithMobile($query, $value)
    {
        return $query->where('mobile', Phone::number($value));
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at')->where('verified_at', '<=', now());
    }

    public function canReceiveAlphanumericSender()
    {
        return true;   
    }
}
