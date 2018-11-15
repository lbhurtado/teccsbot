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
        'location',
    	'remarks',
    ];

    public function messenger()
    {
    	return $this->belongsTo(Messenger::class);
    }
}
