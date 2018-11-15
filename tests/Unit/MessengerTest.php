<?php

namespace Tests\Unit;

use App\Messenger;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MessengerTest extends TestCase
{
	use RefreshDatabase, WithFaker;

    function setUp()
    {
        parent::setUp();

        $this->withoutEvents();

        $this->faker = $this->makeFaker('en_PH');

        $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);
    }

    /** @test */
    function messenger_can_checkin()
    {
        $messenger = factory(Messenger::class)->create();
        $messenger_id = $messenger->id;
        $longitude = 4.9205266;
        $latitude = 52.3832816;

        $messenger->checkin(compact('longitude', 'latitude'));
        $this->assertDatabaseHas('checkins', compact('messenger_id', 'longitude', 'latitude'));
    }
}
