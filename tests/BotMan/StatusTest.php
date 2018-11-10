<?php

namespace Tests\BotMan;

use App\{User, Operator, Messenger, Phone};
use Tests\TestCase;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StatusTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = '/status';

    private $channel_id;

    private $user;

    private $messenger;

    function setUp()
    {
        parent::setUp();

        $this->withoutEvents();

        $this->user = factory(Operator::class)->create(['mobile' => Phone::number('09189362340')]);

        $this->faker = $this->makeFaker('en_PH');
        $this->channel_id = $this->faker->randomNumber(8);
        $this->messenger = Messenger::create([
            'driver' => 'Telegram', 
            'channel_id' => $this->channel_id
        ]);

        $this->messenger->user()->associate($this->user);
        $this->messenger->save();
    }

    /** @test */
    public function status_successful_run()
    {
        $name = $this->faker->name;
        $reason = $this->faker->sentence;

        $this->bot
            ->setUser(['id' => $this->channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('status.introduction'))
            ->assertQuestion(trans('status.input.name'))
            ->receives($name)
            ->assertQuestion(trans('status.input.reason'))
            ->receives($reason)
            ->receives(trans('status.set'))
            ;

        $model_id = $this->messenger->user->id;
        $model_type = $this->messenger->user->type;

        $this->assertDatabaseHas('statuses', compact('name', 'reason', 'model_type'));
    }
}
