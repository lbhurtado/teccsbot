<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RegisterAuthyService implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

   protected $proto;

    public $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user)
    {        
        $this->user = $user;

        $util = \libphonenumber\PhoneNumberUtil::getInstance();

        $this->proto = $util->parse($this->user->mobile, "PH");
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $authy_id = $this->getAuthyId();

        tap($this->user, function($user) use ($authy_id) {
            $user->forceFill(compact('authy_id'));
        })->save();
    }

    protected function getAuthyId()
    {
        return app('rinvex.authy.user')
            ->register($this->getEmail(), $this->getNumber(), $this->getCountryCode())
            ->get('user')['id'];
    }

    protected function getCountryCode()
    {
        return $this->proto->getCountryCode();
    }

    protected function getNumber()
    {
        return $this->proto->getNationalNumber();
    }

    protected function getEmail()
    {
        return $this->getCountryCode() . $this->getNumber() . "@serbis.io";   
    }
}
