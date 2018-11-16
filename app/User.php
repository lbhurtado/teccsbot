<?php

namespace App;

use App\Traits\IsObservable;
use Kalnoy\Nestedset\NodeTrait;
use Spatie\ModelStatus\HasStatuses;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Tightenco\Parental\ReturnsChildModels;
use Spatie\SchemalessAttributes\SchemalessAttributes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Jobs\{InviteUser, RegisterAuthyService, RequestOTP, VerifyOTP, SendUserAccceptedNotification};

class User extends Authenticatable
{
    use Notifiable;
    use ReturnsChildModels;
    use HasRoles;
    use IsObservable;
    use NodeTrait;
    use HasStatuses;
    
    public static $classes = [
        'admin'      => Admin::class,
        'operator'   => Operator::class,
        'staff'      => Staff::class,
        'subscriber' => Subscriber::class,
        'worker'     => Worker::class,
    ];

    protected $fillable = [
        'name', 'email', 'password', 'mobile', 'type',
    ];

    protected $dates = [
        'created_at', 'updated_at', 'verified_at'
    ];

    public $casts = [
        'extra_attributes' => 'array',
    ];
    
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $guard_name = 'web';

    protected $appends = ['messenger'];

    public static function seed($code, $mobile, $parent)
    {
        if (! $model = static::withMobile($mobile)->first()) {
            // $model = (static::$classes[$code])::create(compact('mobile'));
            $model = (static::$classes[$code])::create(compact('mobile'), $parent);
            // if ($model->wasRecentlyCreated) {
                // $model->appendToNode($parent);
                // $model->save();
            // }
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

    public function routeNotificationForTelegram()
    {
        return optional($this->messengers()->where('driver','Telegram')->first())->channel_id;
    }

    public function routeNotificationForFacebook()
    {
        $id = optional($this->messengers()->where('driver','Facebook')->first())->channel_id;

        return compact('id');
    }

    public function routeNotificationForTelerivet()
    {
        return $this->mobile;
    }

    public function getDefaultMessenger()
    {
        return $this->messengers()->whereIn('driver',['Telegram','Facebook'])->first();
    }

    public function invite($driver)
    {
        InviteUser::dispatch($this, $driver);

        return $this;
    }

    public function accepted(User $downline)
    {
        SendUserAccceptedNotification::dispatch($this, $downline);

        return $this;
    }

    public function register()
    {
        RegisterAuthyService::dispatch($this);

        return $this;
    }

    public function challenge()
    {
        RequestOTP::dispatch($this);
    }

    public function verify($otp)
    {
        VerifyOTP::dispatch($this, $otp);

        return $this;
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

    public function syncTasks($task_titles_array)
    {
        $this->tasks()->delete();
        if (! empty($task_titles_array))
            $this->tasks()->createMany($task_titles_array);

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

    public function getMessengerAttribute()
    {
        return optional($this->getDefaultMessenger())->driver;
    }

    public function messengers()
    {
        return $this->hasMany(Messenger::class);
    }

    public function placements()
    {
        return $this->hasMany(Placement::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
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

    public function getExtraAttributesAttribute(): SchemalessAttributes
    {
       return SchemalessAttributes::createForModel($this, 'extra_attributes');
    }

    public function scopeWithExtraAttributes(): Builder
    {
        return SchemalessAttributes::scopeWithSchemalessAttributes('extra_attributes');
    }
}
