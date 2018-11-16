<?php

namespace App\Channels;

use App\Services\Telerivet;
use Illuminate\Notifications\Notification;

class TelerivetChannel
{
    protected $project;

    /**
     * Channel constructor.
     *
     * @param Telerivet $telerivet
     */
    public function __construct(Telerivet $telerivet)
    {
        $this->project = $telerivet->getProject();
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $mobile = $notifiable->routeNotificationFor('telerivet')) {
            return false;
        }

        $message = $notification->toTelerivet($notifiable);

        $retval = $this->project->sendMessage(array(
            'to_number' => $mobile,
            'content' => $message->content
        ));

        return $retval->status;
    }
}