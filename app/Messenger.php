<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Messenger extends Model
{
    protected $fillable = [
        'driver', 'channel_id', 'first_name', 'last_name', 'wants_notifications',
    ];

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
