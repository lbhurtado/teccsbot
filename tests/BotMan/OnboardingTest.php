<?php

namespace Tests\BotMan;

use Tests\TestCase;
use App\{User, Messenger};
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OnboardingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = '/start';

    private $driver;

    private $channel_id;

    function setUp()
    {
        parent::setUp();

        $this->withoutEvents();

        $this->keyword = $this->faker->sentence;
        $this->faker = $this->makeFaker('en_PH');
        $this->driver = TelegramDriver::DRIVER_NAME;
        $this->channel_id = $this->faker->randomNumber(8);
    }

    /** @test */
    public function a_new_user_will_trigger_the_onboarding_sequence()
    {
        $this->bot
            ->setUser(['id' => $this->channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('onboarding.welcome', ['name' => config('app.name')]))
            ;

        $this->assertDatabaseHas('messengers', [
            'channel_id' => $this->channel_id,
            'driver' => TelegramDriver::DRIVER_NAME,
        ]);
    }

    /** @test */
    public function a_new_user_who_wants_to_stay_updated_should_be_flagged_as_such()
    {
        $this->bot
            ->setUser(['id' => $this->channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('onboarding.welcome', ['name' => config('app.name')]))
            ->assertTemplate(Question::class)
            ->receives('yes')
            ;

        $this->assertDatabaseHas('messengers', [
            'channel_id' => $this->channel_id,
            'driver' => TelegramDriver::DRIVER_NAME,
            'wants_notifications' => true
        ]);
    }

    /** @test */
    public function a_new_user_who_does_not_want_to_stay_updated_should_be_flagged_as_such()
    {
        $this->bot
            ->setUser(['id' => $this->channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('onboarding.welcome', ['name' => config('app.name')]))
            ->assertTemplate(Question::class)
            ->receives('no')
            ;

        $this->assertDatabaseHas('messengers', [
            'channel_id' => $this->channel_id,
            'driver' => TelegramDriver::DRIVER_NAME,
            'wants_notifications' => false
        ]);
    }

    /** @test */
    public function an_existing_user_should_not_trigger_the_onboarding_sequence()
    {
        factory(Messenger::class)->create([
            'channel_id' => $this->channel_id,
            'driver' => TelegramDriver::DRIVER_NAME,
        ]);

        $this->bot
            ->setUser(['id' => $this->channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReplyIsNot(trans('onboarding.welcome', ['name' => config('app.name')]));
    }
}
