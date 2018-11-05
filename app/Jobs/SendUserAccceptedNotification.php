<?php

namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\InvitationAccepted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class SendUserAccceptedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $upline;

    protected $downline;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $upline, User $downline)
    {
        $this->upline = $upline;

        $this->downline = $downline;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->upline->notify(new InvitationAccepted($this->downline));
    }
}
