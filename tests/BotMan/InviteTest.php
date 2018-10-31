<?php

namespace Tests\BotMan;

use Tests\TestCase;
use App\Enum\Permission as Permissions;
use Spatie\Permission\Models\Permission;
use App\{User, Admin, Phone, Messenger, Operator};
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InviteTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = '/invite';

    private $channel_id;

    private $messenger;

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

        $user = Operator::create(['mobile' => Phone::number($this->faker->unique()->mobileNumber)]);
        $this->messenger->user()->associate($user);
        $this->messenger->save();
    }

    /** @test */
    public function invite_successful_run()
    {
        $code = 'operator';
        $mobile = Phone::number('09181111111');

        \Queue::fake();
        $this->bot
            ->setUser(['id' => $this->channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('invite.introduction'))
            ->assertQuestion(trans('invite.input.code'))
            ->receives($code)
            ->assertQuestion(trans('invite.input.mobile'))
            ->receives($mobile)
            ->assertQuestion(trans('invite.input.verify', compact('code','mobile')))
            ->receives('Yes')
            ;

        $this->assertDatabaseHas('users', ['mobile' => $mobile, 'type' => User::$classes[$code]]);

        \Queue::assertPushed(\App\Jobs\InviteUser::class);
    }

    /** @test */
    public function invite_successful_even_if_user_exists()
    {
        $code = 'operator';
        $mobile = Phone::number('09181111111');

        User::seed($code, $mobile);

        \Queue::fake();
        $this->bot
            ->setUser(['id' => $this->channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('invite.introduction'))
            ->assertQuestion(trans('invite.input.code'))
            ->receives($code)
            ->assertQuestion(trans('invite.input.mobile'))
            ->receives($mobile)
            ->assertQuestion(trans('invite.input.verify', compact('code','mobile')))
            ->receives('Yes')
            ;

        $this->assertDatabaseHas('users', ['mobile' => $mobile, 'type' => User::$classes[$code]]);

        \Queue::assertPushed(\App\Jobs\InviteUser::class);
    }

    /** @test */
    public function invite_invalid_code_ask_again()
    {
        $invalid_code = 'xxx';
        $code = 'operator';
        $mobile = Phone::number('09181111111');

        User::seed($code, $mobile);
        
        \Queue::fake();
        $this->bot
            ->setUser(['id' => $this->channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('invite.introduction'))
            ->assertQuestion(trans('invite.input.code'))
            ->receives($invalid_code)
            ->assertReply(trans('invite.input.code'))
            ->receives($code)
            ->assertQuestion(trans('invite.input.mobile'))
            ->receives($mobile)
            ->assertQuestion(trans('invite.input.verify', compact('code','mobile')))
            ->receives('Yes')
            ;

        $this->assertDatabaseHas('users', ['mobile' => $mobile, 'type' => User::$classes[$code]]);

        \Queue::assertPushed(\App\Jobs\InviteUser::class);
    }

    /** @test */
    public function invite_invalid_mobile_ask_again()
    {

        $code = 'operator';
        $invalid_mobile = '111';
        $mobile = Phone::number('09181111111');

        User::seed($code, $mobile);
        
        \Queue::fake();
        $this->bot
            ->setUser(['id' => $this->channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('invite.introduction'))
            ->assertQuestion(trans('invite.input.code'))
            ->receives($code)
            ->assertQuestion(trans('invite.input.mobile'))
            ->receives($invalid_mobile)
            ->assertReply(trans('invite.input.mobile'))
            ->receives($mobile)
            ->assertQuestion(trans('invite.input.verify', compact('code','mobile')))
            ->receives('Yes')
            ;

        $this->assertDatabaseHas('users', ['mobile' => $mobile, 'type' => User::$classes[$code]]);

        \Queue::assertPushed(\App\Jobs\InviteUser::class);
    }

    /** @test */
    public function only_user_with_permission_can_invite_with_specific_code()
    {
        $wrong_code = 'admin';
        $code = 'operator';
        $mobile = Phone::number($this->faker->mobileNumber);

        User::seed($code, $mobile);
        
        \Queue::fake();
        $this->bot
            ->setUser(['id' => $this->channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('invite.introduction'))
            ->assertQuestion(trans('invite.input.code'))
            ->receives($wrong_code)
            ->assertReply(trans('invite.error.permission'))
            ->assertReply(trans('invite.input.code'))
            ->receives($code)
            ->assertQuestion(trans('invite.input.mobile'))
            ->receives($mobile)
            ->assertQuestion(trans('invite.input.verify', compact('code','mobile')))
            ->receives('Yes')
            ;

        $this->assertDatabaseHas('users', ['mobile' => $mobile, 'type' => User::$classes[$code]]);

        \Queue::assertPushed(\App\Jobs\InviteUser::class);
    }
}
