<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Channels\{TelerivetChannel, TelerivetMessage};

class Welcome extends Notification
{
    use Queueable;

    protected $content;

    public function __construct()
    {
        $this->content = trans('onboarding.welcome');
    }

    public function via($notifiable)
    {
        return [TelerivetChannel::class];
    }

    public function toTelerivet($notifiable)
    {
        return TelerivetMessage::create()
            ->content($this->content)
            ;
    }
}
