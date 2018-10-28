<?php

namespace App\Observers;

use App\User;
use App\Events\UserEvent;
use App\Events\User\UserEvents;


class UserObserver
{
    /**
     * Handle the user "created" event.
     *
     * @param  \App\=App\User  $user
     * @return void
     */
    public function created(User $user)
    {
        event(UserEvents::CREATED, new UserEvent($user));
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        //
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Handle the user "restored" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
