<?php

namespace App\Listeners;

use App\Events\UserWasRecorded;
use App\Jobs\RegisterAuthyService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserMobileData
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserWasRecorded  $event
     * @return void
     */
    public function handle(UserWasRecorded $event)
    {
        RegisterAuthyService::dispatch($event->user);
    }
}
