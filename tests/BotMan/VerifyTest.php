<?php

namespace Tests\BotMan;

use App\{User, Operator, Messenger, Phone};
use Tests\TestCase;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VerifyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = '/verify';

    private $channel_id;

    private $admin;

    function setUp()
    {
        parent::setUp();

        $this->withoutEvents();

        $this->faker = $this->makeFaker('en_PH');

        $this->channel_id = $this->faker->randomNumber(8);
        $this->messenger = Messenger::create([
            'driver' => 'Telegram', 
            'channel_id' => $this->channel_id
        ]);

        // just to create the permissions
        $this->admin = factory(\App\Admin::class)->create(['name' => 'Admin']);

        // InvalidArgumentException: Unknown setter 'date'
        // $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);
    }

    /** @test */
    public function verify_successful_run_with_verify_ask_again()
    {
        \Queue::fake();

        $name = $this->faker->name;
        $mobile = Phone::number('09178251991');
        $authy_id = '106530563';
        $driver = TelegramDriver::DRIVER_NAME;
        $channel_id = $this->faker->randomNumber(8);
        $pin = $this->faker->randomNumber(6);
        $affirmative = 'Yes';
        $negative = 'No';

        $user = factory(Operator::class)->create(compact('mobile', 'authy_id'));
        $this->admin->appendNode($user);

        $this->bot
            ->setUser(['id' => $this->channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('verify.introduction'))
            // ->assertQuestion(trans('verify.input.name', ['name' => $this->messenger->name]))
            ->assertTemplate(Question::class)
            ->receives($name)
            ->assertQuestion(trans('verify.input.mobile'))
            ->receives($mobile)
            ->assertQuestion(trans('verify.input.verify', compact('name', 'mobile')))
            ->receivesInteractiveMessage($negative)
            ->assertQuestion(trans('verify.input.mobile'))
            ->receives($mobile)
            ->assertQuestion(trans('verify.input.verify', compact('name', 'mobile')))
            ->receivesInteractiveMessage($affirmative)
            ;

        \Queue::assertPushed(\App\Jobs\RequestOTP::class);

        $user->verifiedBy($pin, false);

        $this->bot
            ->assertQuestion(trans('verify.input.pin'))
            ->receives($pin)                        
            ;

        // $user->verifiedBy($pin, false); //this is supposed to be here but it doesn't work
        \Queue::assertPushed(\App\Jobs\VerifyOTP::class);
        \Queue::assertPushed(\App\Jobs\SendUserAccceptedNotification::class);

        $this->bot
            ->assertReply(trans('verify.success'))
            ;

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
    public function verify_invalid_mobile_ask_again()
    {
        \Queue::fake();

        $name = $this->faker->name;
        $invalid_mobile = '111';
        $mobile = Phone::number('09178251991');
        $authy_id = '106530563';
        $driver = TelegramDriver::DRIVER_NAME;
        $channel_id = $this->faker->randomNumber(8);
        $wrong_pin = $this->faker->randomNumber(6);
        $pin = $this->faker->randomNumber(6);
        $affirmative = 'Yes';
        $negative = 'No';

        $user = factory(Operator::class)->create(compact('mobile', 'authy_id'));
        $this->admin->appendNode($user);

        $this->bot
            ->setUser(['id' => $this->channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('verify.introduction'))
            // ->assertQuestion(trans('verify.input.name', ['name' => $this->messenger->name]))
            ->assertTemplate(Question::class)
            ->receives($name)
            ->assertQuestion(trans('verify.input.mobile'))
            ->receives($invalid_mobile)
            ->assertReply(trans('verify.input.mobile'))
            ->receives($mobile)
            // ->assertQuestion(trans('verify.input.verify', compact('mobile')))
            ->assertQuestion(trans('verify.input.verify', compact('name', 'mobile')))
            ->receivesInteractiveMessage($affirmative)
            ;

        \Queue::assertPushed(\App\Jobs\RequestOTP::class);

        $user->verifiedBy($pin, false);

        $this->bot
            ->assertQuestion(trans('verify.input.pin'))
            ->receives($pin)                        
            ;

        \Queue::assertPushed(\App\Jobs\VerifyOTP::class);
        \Queue::assertPushed(\App\Jobs\SendUserAccceptedNotification::class);
        $this->bot
            ->assertReply(trans('verify.success'))
            ;
    }

    /** @test */
    public function verify_invalid_pin_ask_again()
    {
        \Queue::fake();

        $name = $this->faker->name;
        $mobile = Phone::number('09178251991');
        $authy_id = '106530563';
        $driver = TelegramDriver::DRIVER_NAME;
        $channel_id = $this->faker->randomNumber(8);
        $wrong_pin = $this->faker->randomNumber(6);
        $pin = $this->faker->randomNumber(6);
        $affirmative = 'Yes';
        $negative = 'No';

        $user = factory(Operator::class)->create(compact('mobile', 'authy_id'));
        $this->admin->appendNode($user);

        $this->bot
            ->setUser(['id' => $this->channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('verify.introduction'))
            ;

        $this->messenger->refresh;

        $this->bot
            // ->assertQuestion(trans('verify.input.name', ['name' => $this->bot->getUser()->getFirstName()]))
            ->assertTemplate(Question::class)
            ->receives($name)
            ->assertQuestion(trans('verify.input.mobile'))
            ->receives($mobile)
            // ->assertQuestion(trans('verify.input.verify', compact('mobile')))
            ->assertQuestion(trans('verify.input.verify', compact('name', 'mobile')))
            ->receivesInteractiveMessage($negative)
            ->assertQuestion(trans('verify.input.mobile'))
            ->receives($mobile)
            // ->assertQuestion(trans('verify.input.verify', compact('mobile')))
            ->assertQuestion(trans('verify.input.verify', compact('name', 'mobile')))
            ->receivesInteractiveMessage($affirmative)
            ;

        \Queue::assertPushed(\App\Jobs\RequestOTP::class);

        // $user->verifiedBy($pin, false);  //simulates a wrong pin

        $this->bot
            ->assertQuestion(trans('verify.input.pin'))
            ->receives($wrong_pin)     
            ->assertReply(trans('verify.fail'))                   
            ;

        \Queue::assertPushed(\App\Jobs\VerifyOTP::class);
        $user->verifiedBy($pin, false);

        $this->bot
            ->assertQuestion(trans('verify.input.pin'))
            ->receives($pin)                        
            ->assertReply(trans('verify.success'))
            ;
        \Queue::assertPushed(\App\Jobs\SendUserAccceptedNotification::class);
    }
}
