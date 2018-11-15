<?php

namespace Tests\Unit;

use App\Messenger;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Support\Facades\Geocoder;

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
        $longitude = 121.17332;
        $latitude = 13.928264;

        $messenger->checkin(compact('longitude', 'latitude'));
        $this->assertDatabaseHas('checkins', compact('messenger_id', 'longitude', 'latitude'));

        // $geocoder = new Geocoder();
        // \Geocoder::getAddressForCoordinates($latitude, $longitude);

        // dd(\Geocoder::getAddressForCoordinates($latitude, $longitude)['formatted_address']);
    }
}
