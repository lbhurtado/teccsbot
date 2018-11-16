<?php

namespace App\Listeners\User;

use App\Events\CheckinEvent;
use App\Events\User\CheckinEvents;
use App\Jobs\ReverseGeocode;


class CheckinEventSubscriber
{
    /**
     * @param CheckinEvent $event
     */
    public function onCheckinCreating(CheckinEvent $event)
    {
        $checkin = $event->getCheckin();
    }

    /**
     * @param CheckinEvent $event
     */
    public function onCheckinCreated(CheckinEvent $event)
    {
    	$checkin = $event->getCheckin();

        ReverseGeocode::dispatch($checkin);
    }

    /**
     * @param CheckinEvent $event
     */
    public function onCheckinUpdated(CheckinEvent $event)
    {
    	$checkin = $event->getCheckin();
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            CheckinEvents::CREATING, 
            CheckinEventSubscriber::class.'@onCheckinCreating'
        );

        $events->listen(
            CheckinEvents::CREATED, 
            CheckinEventSubscriber::class.'@onCheckinCreated'
        );

        $events->listen(
            CheckinEvents::UPDATED,
            CheckinEventSubscriber::class.'@onCheckinUpdated'
        );
    }    


}