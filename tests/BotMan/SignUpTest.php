<?php

namespace Tests\BotMan;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use BotMan\Drivers\Telegram\TelegramDriver;
use BotMan\BotMan\Messages\Outgoing\{Question, OutgoingMessage};
use App\{Admin, Tag, Placement, User};
use Carbon\Carbon;

class SignUpTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;

    // protected $user;

    function setUp()
    {
        parent::setUp();

        $this->withoutEvents();

        $this->faker = $this->makeFaker('en_PH');

        $this->admin = factory(\App\Admin::class)->create();

        foreach(Tag::$classes as $key => $values) {
            $code = $key;
            $type = $values;
            $message = env('BOT_REGISTRATION_MESSAGE_'.strtoupper($key), 'You are now a registered '.strtolower($key).'.');
            Placement::record(compact('code', 'type', 'message'), $this->admin);      
        }

        // InvalidArgumentException: Unknown setter 'date'
        // $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);
    }

    /** @test */
    public function signup_inputs_mobile_code_pin_new_user()
    {
        \Queue::fake();

        $mobile = '+639181111111';
        $type = 'operator';

        $this->bot
            ->setUser(['id' => 111111])
            ->setDriver(TelegramDriver::class)
            ->receives('signup')
            ->assertQuestion('Please enter mobile number.') 
            ->receives($mobile)
            ->assertQuestion('Please enter your code.')
            ->receives($type)
            ->assertQuestion('Please enter your PIN.')
            ;

        $user = User::where(compact('mobile'))->first();
        $user->forceFill(['verified_at' => date("Y-m-d H:i:s")])->save();
        $placement = Placement::bearing($type)->first();

        $this->bot
            ->receives('123456')
            ->assertReply($placement->message)
            ;

        \Queue::assertPushed(\App\Jobs\RequestOTP::class);
        \Queue::assertPushed(\App\Jobs\VerifyOTP::class);
    }

    /** @test */
    public function signup_inputs_mobile_old_verified_user()
    {
        $mobile = '09182222222';
        $user = factory(\App\Operator::class)->create(compact('mobile'));

        $user->forceFill(['verified_at' => date("Y-m-d H:i:s")])->save();

        $this->bot
            ->setUser(['id' => 222222])
            ->setDriver(TelegramDriver::class)
            ->receives('signup')
            ->assertQuestion('Please enter mobile number.') 
            ->receives($mobile)
            ->assertReply('You are still valid.')
            ;
    }

    /** @test */
    public function signup_inputs_mobile_old_verification_stale_user()
    {
        $mobile = '09183333333';
        $user = factory(\App\Operator::class)->create(compact('mobile'));

        $date = new \DateTime();
        $date->sub(new \DateInterval('P10D'));
        $user->forceFill(['verified_at' => $date])->save();

        $this->bot
            ->setUser(['id' => 333333])
            ->setDriver(TelegramDriver::class)
            ->receives('signup')
            ->assertQuestion('Please enter mobile number.') 
            ->receives($mobile)
            ->assertQuestion('Please enter your PIN.')
            ;
    }
}
