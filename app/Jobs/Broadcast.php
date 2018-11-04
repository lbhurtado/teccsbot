<?php

namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use App\Notifications\OnDemand;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Broadcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    protected $message;

    protected $including_sms;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $message, $including_sms = false)
    {
        $this->user = $user;

        $this->message = $message;

        $this->including_sms = $including_sms;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $downline = $this->including_sms == true
                    ? User::defaultOrder()->descendantsOf($this->user)
                    : User::defaultOrder()->descendantsOf($this->user)->where('messenger', '!=', null)
                    ;

        \Notification::send($downline, new OnDemand($this->message));
    }
}
