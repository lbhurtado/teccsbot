<?php

namespace App\Observers;

use App\User;
use App\Events\UserEvent;
use App\Events\User\UserEvents;


class UserObserver
{
    /**
     * Handle the user "creating" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function creating(User $user)
    {
        $user->password = $user->password ?? bcrypt('1234'); 
        
        event(UserEvents::CREATING, new UserEvent($user));

    }

    /**
     * Handle the user "created" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function created(User $user)
    {
        event(UserEvents::CREATED, new UserEvent($user));
    }

    /**
     * Handle the user "updating" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function updating(User $user)
    {
        event(UserEvents::UPDATING, new UserEvent($user));
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        event(UserEvents::UPDATED, new UserEvent($user));
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
