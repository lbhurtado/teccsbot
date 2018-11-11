<?php

namespace App\Listeners\User;

use App\Events\UserEvent;
use App\Events\User\UserEvents;
use App\Jobs\GenerateUserTasks;
use App\Jobs\RegisterAuthyService;


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

        // $class = get_class($user);
        // if (property_exists($class, 'role'))
        //     if ($role = $class::$role)
        //         if (array_key_exists($role, config('chatbot.tasks')))
                    GenerateUserTasks::dispatch($user);
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