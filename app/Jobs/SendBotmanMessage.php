<?php

namespace App\Jobs;

use App\Messenger;
use BotMan\BotMan\BotMan;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendBotmanMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $bot;

    protected $messenger;

    protected $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(BotMan $bot, Messenger $messenger, $message)
    {
        $this->bot = $bot;

        $this->messenger = $messenger;

        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $driver = $this->messenger->getDriverClass();

        $this->bot->say($this->message, $this->messenger->channel_id, $driver);
    }
}
