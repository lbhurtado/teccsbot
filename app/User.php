<?php

namespace App;

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
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'mobile'
    ];

    protected $dates = [
        'created_at', 'updated_at', 'verified_at'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
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

    public function scopeWithMobile($query, $value)
    {
        return $query->where('mobile', Phone::number($value));
    }
}
