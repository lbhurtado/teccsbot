<?php

namespace Tests\BotMan;

use Tests\TestCase;
use App\{Admin, Placement, User};
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use BotMan\BotMan\Messages\Outgoing\{Question, OutgoingMessage};

class RegisterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $admin;

    function setUp()
    {
        parent::setUp();

        $this->withoutEvents();

        $this->faker = $this->makeFaker('en_PH');

        $this->admin = factory(\App\Admin::class)->create(['name' => 'Admin']);

        foreach(User::$classes as $key => $values) {
            $code = $key;
            $type = $values;
            $message = env('BOT_REGISTRATION_MESSAGE_'.strtoupper($key), 'You are now a registered '.strtolower($key).'.');
            Placement::record(compact('code', 'type', 'message'), $this->admin);      
        }
        // InvalidArgumentException: Unknown setter 'date'
        // $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);
    }

    /** @test */
    public function register_inputs_mobile_code_successful_new_user()
    {
        \Queue::fake();

        $name = $this->faker->name;
        $number = '09178251991';
        $code = 'Operator';

        $this->bot
            ->setUser(['id' => 111111])
            ->setDriver(TelegramDriver::class)
            ->receives("register $code $number")
            ->assertReply("OTP sent.") 
            ;

        \Queue::assertPushed(\App\Jobs\RequestOTP::class);
    }

    /** @test */
    public function register_inputs_mobile_code_failed_no_user()
    {
        \Queue::fake();

        $number = '09178251991';
        $code = $this->faker->sentence;

        $this->bot
            ->setUser(['id' => 111111])
            ->setDriver(TelegramDriver::class)
            ->receives("register $code $number")
            ->assertReply("Try again.") 
            ;

        $number = $this->faker->name;
        $code = 'Operator';

        $this->bot
            ->setUser(['id' => 111111])
            ->setDriver(TelegramDriver::class)
            ->receives("register $code $number")
            ->assertReply("Try again.") 
            ;


        $number = '09178251991';
        $code = 'Operator';

        $this->bot
            ->setUser(['id' => 111111])
            ->setDriver(TelegramDriver::class)
            ->receives("register $number $code") //baliktad
            ->assertReply("Try again.") 
            ;

        \Queue::assertNotPushed(\App\Jobs\RequestOTP::class);
    }
}
