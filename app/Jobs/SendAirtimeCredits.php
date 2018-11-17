<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Notifications\LoadCredits;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendAirtimeCredits implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->user->notify(new LoadCredits());
        if (! $this->user->extra_attributes->loaded) {
            $this->user->extra_attributes->loaded = true;
            $this->user->save();            
        }
    }
}
