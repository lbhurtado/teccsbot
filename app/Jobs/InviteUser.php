<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Notifications\UserInvitation;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class InviteUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    protected $driver;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $driver)
    {
        $this->user = $user;

        $this->driver = $driver;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->user->notify(new UserInvitation($this->driver));
    }
}
