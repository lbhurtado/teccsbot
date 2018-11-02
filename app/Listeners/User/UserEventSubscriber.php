<?php

namespace App\Listeners\User;

use App\Events\UserEvent;
use App\Events\User\UserEvents;
use App\Jobs\RegisterAuthyService;
use App\Jobs\GenerateUserPlacements;

class UserEventSubscriber
{
    /**
     * @param UserEvent $event
     */
    public function onUserCreating(UserEvent $event)
    {
        $user = $event->getUser();
    }

    /**
     * @param UserEvent $event
     */
    public function onUserCreated(UserEvent $event)
    {
    	$user = $event->getUser();

    	RegisterAuthyService::dispatch($user);
    	// GenerateUserPlacements::dispatch($user);
    }

    /**
     * @param UserEvent $event
     */
    public function onUserUpdated(UserEvent $event)
    {
    	$user = $event->getUser();
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            UserEvents::CREATING, 
            UserEventSubscriber::class.'@onUserCreating'
        );

        $events->listen(
            UserEvents::CREATED, 
            UserEventSubscriber::class.'@onUserCreated'
        );

        $events->listen(
            UserEvents::UPDATED,
            UserEventSubscriber::class.'@onUserUpdated'
        );
    }    


}