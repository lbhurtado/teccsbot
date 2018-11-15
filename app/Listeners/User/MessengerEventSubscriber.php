<?php

namespace App\Listeners\User;

use App\Events\MessengerEvent;
use App\Events\User\MessengerEvents;
use App\Jobs\ReverseGeocode;


class MessengerEventSubscriber
{
    /**
     * @param UserEvent $event
     */
    public function onUserCreating(MessengerEvent $event)
    {
        $messenger = $event->getMessenger();
    }

    /**
     * @param UserEvent $event
     */
    public function onUserCreated(MessengerEvent $event)
    {
    	$messenger = $event->getMessenger();

        ReverseGeocode::dispatch($messenger);
    }

    /**
     * @param UserEvent $event
     */
    public function onUserUpdated(MessengerEvent $event)
    {
    	$messenger = $event->getMessenger();
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            MessengerEvents::CREATING, 
            MessengerEventSubscriber::class.'@onUserCreating'
        );

        $events->listen(
            MessengerEvents::CREATED, 
            MessengerEventSubscriber::class.'@onUserCreated'
        );

        $events->listen(
            MessengerEvents::UPDATED,
            MessengerEventSubscriber::class.'@onUserUpdated'
        );
    }    


}