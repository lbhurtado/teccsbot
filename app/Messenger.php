<?php

namespace App;

use Spatie\ModelStatus\HasStatuses;
use Illuminate\Database\Eloquent\Model;
use BotMan\Drivers\Facebook\WebDriver;
use BotMan\Drivers\Telegram\TelegramDriver;
use BotMan\Drivers\Facebook\FacebookDriver;

class Messenger extends Model
{
    use HasStatuses;
    
    protected $fillable = [
        'driver', 'channel_id', 'first_name', 'last_name', 'wants_notifications',
    ];

    // public static function instance($driver, $channel_id)
    // {
    //     return app(Messenger::class)->getInstance($driver, $channel_id);
    // }


    // public function getInstance($driver, $channel_id)
    // {
    //     static $instance = null;

    //     if (null === $instance) {
    //         $instance = static::where(compact('driver', 'channel_id'))->first();
    //     }

    //     return $instance;
    // }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     * https://laravel-taiwan.github.io/php-the-right-way/pages/Design-Patterns.html
     *
     * @return void
     */
    private function __clone(){}

    public function getDriverClass()
    {
        $driverClass = '';
        switch ($this->getDriver())
        {
            case 'telegram':
                $driverClass = TelegramDriver::class;
                break;
            case 'facebook':
                $driverClass = FacebookDriver::class;
                break;
            default:
                $driverClass = WebDriver::class;
                break;
        }

        return $driverClass;
    }

    public function getDriver()
    {
        return strtolower($this->driver);
    }

    public function turnOnNotifications()
    {
        $this->update(['wants_notifications' => true]);
    }

    public function checkin(...$coordinates)
    {
        $coordinates = array_flatten($coordinates);
        $longitude = $coordinates[0];
        $latitude = $coordinates[1];
        // $remarks = $coordinates[2]

        $checkin = $this->checkins()->create(compact('longitude', 'latitude'));

        return $checkin;
    }
    
    public function scopeWantsUpdates($query)
    {
        return $query->where('wants_notifications', true);
    }

    public function getNameAttribute()
    {
        return trim(ucfirst($this->first_name . ' ' . $this->last_name));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function checkins()
    {
        return $this->hasMany(Checkin::class);
    }
}
