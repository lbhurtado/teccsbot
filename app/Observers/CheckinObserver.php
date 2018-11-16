<?php

namespace App\Observers;

use App\Checkin;
use App\Events\CheckinEvent;
use App\Events\User\CheckinEvents;

class CheckinObserver
{
    /**
     * Handle the Checkin "creating" event.
     *
     * @param  \App\Checkin  $Checkin
     * @return void
     */
    public function creating(Checkin $checkin)
    {   
        event(CheckinEvents::CREATING, new CheckinEvent($checkin));

    }

    /**
     * Handle the Checkin "created" event.
     *
     * @param  \App\Checkin  $checkin
     * @return void
     */
    public function created(Checkin $checkin)
    {
        event(CheckinEvents::CREATED, new CheckinEvent($checkin));
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\Checkin  $checkin
     * @return void
     */
    public function updated(Checkin $checkin)
    {
        //
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\Checkin  $checkin
     * @return void
     */
    public function deleted(Checkin $checkin)
    {
        //
    }

    /**
     * Handle the user "restored" event.
     *
     * @param  \App\Checkin  $Checkin
     * @return void
     */
    public function restored(Checkin $checkin)
    {
        //
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param  \App\Checkin  $Checkin
     * @return void
     */
    public function forceDeleted(Checkin $checkin)
    {
        //
    }
}
