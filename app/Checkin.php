<?php

namespace App;

// use Malhal\Geographical\Geographical;
use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    // use Geographical;

    protected static $kilometers = true;
    
    protected $fillable = [
    	'longitude',
    	'latitude',
    	'remarks',
    ];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
