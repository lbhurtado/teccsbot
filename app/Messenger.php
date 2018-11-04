<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use BotMan\Drivers\Facebook\WebDriver;
use BotMan\Drivers\Telegram\TelegramDriver;
use BotMan\Drivers\Facebook\FacebookDriver;

class Messenger extends Model
{
    protected $fillable = [
        'driver', 'channel_id', 'first_name', 'last_name', 'wants_notifications',
    ];

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

    public function scopeWantsUpdates($query)
    {
        return $query->where('wants_notifications', true);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
