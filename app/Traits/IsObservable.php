<?php

namespace App\Traits;

use App\Observers\UserObserver;

trait IsObservable
{
    protected static function bootIsObservable()
    {
        // you MUST call the parent boot method 
        // in this case the \Illuminate\Database\Eloquent\Model
        // parent::boot(); // LBH: but probably not in the trait

        // note I am using static::observe(...) instead of Config::observe(...)
        // this way the child classes auto-register the observer to their own class
        static::observe(UserObserver::class);
    }
}