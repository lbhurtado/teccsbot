<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\{PhoneVerification, SendOTP};

class RequestOTP implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;

    private $otp;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $otp)
    {
        $this->user = $user;

        $this->otp = $otp;
    }

    /**
     * Execute the job.
     *
     * @return void
     // */
    public function handle()
    {
        $this->user->notify(new SendOTP($this->otp));
        // $this->user->notify(new PhoneVerification('sms', true));
        // $this->user->notify(new PhoneVerification('sms', true, 't.me/grassroots_bot?verify='.$this->user->mobile));
    }
}
