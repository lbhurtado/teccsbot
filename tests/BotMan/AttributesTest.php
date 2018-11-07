<?php

namespace Tests\BotMan;

use Tests\TestCase;
use App\{User, Messenger, Phone};
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttributesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = '$';

    private $channel_id;

    private $messenger;

    protected $user;

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

        $user = User::create(['mobile' => Phone::number('09189362340')]);
        $this->messenger->user()->associate($user);
        $this->messenger->save();
    }

    /** @test */
    function user_can_assign_values_arbitrarily()
    {
        $query_string = "abc=123&def=456";
        $set_keyword = "?$query_string";
        parse_str($query_string, $associative_array);
        $attributes = http_build_query($associative_array);
        $var = "abc";
        $attribute = http_build_query(['abc' => '123']);
        $get_keyword = "\$$var";
        $this->bot
            ->setUser(['id' => $this->channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($set_keyword)
            ->assertReply(trans('attributes.set', compact('attributes')))
            ->receives($get_keyword)
            ->assertReply(trans('attributes.get', compact('attribute')))
            ;

        $this->assertDatabaseHas('users', [
            'extra_attributes' => json_encode($associative_array),
        ]);
    }

}
