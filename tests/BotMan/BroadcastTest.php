<?php

namespace Tests\BotMan;

use Tests\TestCase;
use App\{Admin, Placement, User};
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BroadcastTest extends TestCase
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
    public function user_can_broadcast()
    {
        Placement::activate('operator', ['mobile' => '09178251991'])->verifiedBy('asds', false);
        Placement::activate('staff', ['mobile' => '09189362340'])->verifiedBy('asds', false);
        Placement::activate('worker', ['mobile' => '09175180722'])->verifiedBy('asds', false);

        $this->bot->receives('broadcast Hello there.')
            ->assertReply('Broadcast sent.')
            ;
    }
}
