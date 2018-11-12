<?php

namespace Tests\BotMan;

use Tests\TestCase;
use App\{User, Operator, Messenger, Phone};
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = '/task';
    private $user;
    private $channel_id;
    private $messenger;

    function setUp()
    {
        parent::setUp();
        $this->faker = $this->makeFaker('en_PH');
        $this->withoutEvents();

        $this->user = factory(Operator::class)->create(['mobile' => Phone::number('09189362340')]);
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
        $name = $this->faker->name;
        $reason = $this->faker->sentence;

        $this->bot
            ->setUser(['id' => $this->channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('task.introduction', ['count' => $this->user->tasks()->available()->get()->count()]))
            ->assertQuestion(trans('task.choose.task'))
            // ->receives($name)
            // ->assertQuestion(trans('status.input.reason'))
            // ->receives($reason)
            // ->receives(trans('status.set'))
            ;
    }
}
