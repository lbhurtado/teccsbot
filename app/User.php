<?php

namespace App;

use App\Jobs\RequestOTP;
use App\Traits\IsObservable;
use Kalnoy\Nestedset\NodeTrait;
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
 
    /**
     * Route notifications for the Authy channel.
     *
     * @return int
     */
    public function routeNotificationForAuthy()
    {
        return $this->authy_id;
    }

    public function verify()
    {
        RequestOTP::dispatch($this);
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
}
