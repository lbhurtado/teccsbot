<?php

namespace Tests\BotMan;

use App\{User, Operator, Placement};
use Tests\TestCase;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VerifyTest extends TestCase
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
    public function verify_inputs_number_otp()
    {
        \Queue::fake();

        $mobile = '09178251991';
        $authy_id = '106530563';

        $user = factory(Operator::class)->create(compact('mobile', 'authy_id'));
        $this->admin->appendNode($user);

        $this->bot
            ->setUser(['id' => 111111])
            ->setDriver(TelegramDriver::class)
            ->receives("verify")
            ->assertQuestion("Please enter mobile number.") 
            ->receives($mobile)
            ->assertQuestion("Please enter your PIN.") 
            ;

        $user = User::withMobile($mobile)->first();
        $user->forceFill(['verified_at' => date("Y-m-d H:i:s")])->save();

        // dd($user->isVerified());

        $this->bot
            ->receives('123456')
            ->assertReply("Yehey!") 
            ;

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
}
