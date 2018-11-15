<?php

namespace Tests\BotMan;

use Tests\TestCase;
use App\{User, Worker, Messenger, Phone};
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckinTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = '/checkin';
    private $user;
    private $channel_id;
    private $messenger;

    function setUp()
    {
        parent::setUp();
        $this->faker = $this->makeFaker('en_PH');
        $this->withoutEvents();

        $this->user = factory(Worker::class)->create(['mobile' => Phone::number('09189362340')]);
        $this->channel_id = $this->faker->randomNumber(8);
        $this->messenger = Messenger::create([
            'driver' => 'Telegram', 
            'channel_id' => $this->channel_id
        ]);

        $this->messenger->user()->associate($this->user);
        $this->messenger->save();
    }

    /** @test */
    public function task_successful_run()
    {
        $this->bot
            ->setUser(['id' => $this->channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('checkin.introduction', ['name' => $this->user->name]))
            ;
    }
}
