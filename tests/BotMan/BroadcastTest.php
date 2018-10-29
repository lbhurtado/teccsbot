<?php

namespace Tests\BotMan;

use Tests\TestCase;
use App\Jobs\SendBotmanMessage;
use App\{Admin, Placement, User, Messenger};
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


class BroadcastTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = '/broadcast';

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
    public function user_can_broadcast()
    {
        \Queue::fake();

        $messenger = Messenger::create([
            'driver' => 'Facebook',
            'channel_id' => '111111',
        ]);

        $user1 = Placement::activate('operator', ['mobile' => '09178251991'])->verifiedBy('asds', false);

        $messenger->user()->associate($user1)->save();

        Placement::activate('staff', ['mobile' => '09189362340'])->verifiedBy('asds', false);
        Placement::activate('worker', ['mobile' => '09175180722'])->verifiedBy('asds', false);

        $this->bot->receives("{$this->keyword} Hello there.")
            ->assertReply('Broadcast sent.')
            ;

        \Queue::assertPushed(\App\Jobs\SendBotmanMessage::class);
    }
}
