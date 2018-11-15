<?php

namespace App\Observers;

use App\Messenger;
use App\Events\MessengerEvent;
use App\Events\User\MessengerEvents;

class ChekinObserver
{
    /**
     * Handle the messenger "creating" event.
     *
     * @param  \App\Messenger  $messenger
     * @return void
     */
    public function creating(Messenger $messenger)
    {   
        event(MessengerEvents::CREATING, new MessengerEvent($messenger));

    }

    /**
     * Handle the messenger "created" event.
     *
     * @param  \App\Messenger  $messenger
     * @return void
     */
    public function created(Messenger $messenger)
    {
        event(MessengerEvents::CREATED, new MessengerEvent($messenger));
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\Messenger  $messenger
     * @return void
     */
    public function updated(Messenger $messenger)
    {
        //
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\Messenger  $messenger
     * @return void
     */
    public function deleted(Messenger $messenger)
    {
        //
    }

    /**
     * Handle the user "restored" event.
     *
     * @param  \App\Messenger  $messenger
     * @return void
     */
    public function restored(Messenger $messenger)
    {
        //
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param  \App\Messenger  $messenger
     * @return void
     */
    public function forceDeleted(Messenger $messenger)
    {
        //
    }
}
