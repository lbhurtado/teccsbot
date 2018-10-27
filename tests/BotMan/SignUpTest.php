<?php

namespace Tests\BotMan;

use Tests\TestCase;
use App\{Admin, Tag, Placement, User};
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use BotMan\BotMan\Messages\Outgoing\{Question, OutgoingMessage};



class SignUpTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;

    protected $phone_numbers = [
        '09181111111',
        '09182222222',
        '09183333333',
    ];
    // protected $user;

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
    public function signup_inputs_name_mobile_code_pin_new_user()
    {
        \Queue::fake();

        $name = $this->faker->name;
        $number = '09181111111';
        $type = 'Operator';

        $this->bot
            ->setUser(['id' => 111111])
            ->setDriver(TelegramDriver::class)
            ->receives('signup')
            ->assertQuestion('Please enter your name.') 
            ->receives($name)            
            ->assertQuestion('Please enter mobile number.') 
            ->receives($number)
            ->assertQuestion('Please enter your code.')
            ->receives($type)
            ->assertQuestion('Please enter your PIN.')
            ;

        $user = User::withMobile($number)->first();
        $user->forceFill(['verified_at' => date("Y-m-d H:i:s")])->save();
        $placement = Placement::bearing($type)->first();

        $this->bot
            ->receives('123456')
            ->assertReply($placement->message)
            ;

        \Queue::assertPushed(\App\Jobs\RequestOTP::class);
        \Queue::assertPushed(\App\Jobs\VerifyOTP::class);

        $nodes = User::get()->toTree();

        $traverse = function ($categories, $prefix = '-') use (&$traverse) {
            foreach ($categories as $category) {
                echo PHP_EOL.$prefix.' '.$category->name.' ('.$category->mobile.')';

                $traverse($category->children, $prefix.'-');
            }
        };

        $traverse($nodes);
        echo PHP_EOL.' ';
        echo PHP_EOL.' ';
    }

    /** @test */
    public function signup_inputs_name_mobile_old_verified_user()
    {
        $name = $this->faker->name;
        $mobile = '09182222222';
        $user = factory(\App\Operator::class)->create(compact('mobile'));

        $user->forceFill(['verified_at' => date("Y-m-d H:i:s")])->save();

        $this->bot
            ->setUser(['id' => 222222])
            ->setDriver(TelegramDriver::class)
            ->receives('signup')
            ->assertQuestion('Please enter your name.') 
            ->receives($name) 
            ->assertQuestion('Please enter mobile number.') 
            ->receives($mobile)
            ->assertReply('You are still valid.')
            ;
    }

    /** @test */
    public function signup_inputs_name_mobile_old_verification_stale_user()
    {
        $name = $this->faker->name;
        $mobile = '09183333333';
        $user = factory(\App\Operator::class)->create(compact('mobile'));

        $date = new \DateTime();
        $date->sub(new \DateInterval('P10D'));
        $user->forceFill(['verified_at' => $date])->save();

        $this->bot
            ->setUser(['id' => 333333])
            ->setDriver(TelegramDriver::class)
            ->receives('signup')
            ->assertQuestion('Please enter your name.') 
            ->receives($name) 
            ->assertQuestion('Please enter mobile number.') 
            ->receives($mobile)
            ->assertQuestion('Please enter your PIN.')
            ;
    }
}
