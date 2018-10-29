<?php

namespace Tests\BotMan;

use Tests\TestCase;
use App\{Admin, Placement, User, Messenger, Operator, Staff, Worker, Subscriber};
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlacementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = '/placement';
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
    public function placement_outputs_codes()
    {

        $name = $this->faker->name;
        $mobile = '09178251991';
        $code = 'Operator';
        $driver = TelegramDriver::DRIVER_NAME;
        $channel_id = '111111';

        $user = factory(Operator::class)->create(compact('mobile', 'name'));
        Placement::record(['code' => 'abc', 'type' => Operator::class,], $user);
        Placement::record(['code' => 'def', 'type' => Staff::class,], $user);
        Placement::record(['code' => 'ghi', 'type' => Worker::class,], $user);
        Placement::record(['code' => 'jkl', 'type' => Subscriber::class,], $user);

        $this->bot
            ->setUser(['id' => $channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives('Hi')
            ->assertReply('Hello!')
            ;

        $messenger = Messenger::where(compact('driver', 'channel_id'))->first();
        $messenger->user()->associate($user);
        $messenger->save();

        $this->bot
            ->receives($this->keyword)
            ->assertReply("abc,def,ghi,jkl") 
            ;
    }
}
