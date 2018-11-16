<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CheckinEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $checkin;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($checkin)
    {
        $this->checkin = $checkin;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('checkin-event');
    }

    public function getCheckin()
    {
        return $this->checkin;
    }
}
