<?php

namespace Tests\Unit;

use App\Checkin;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Geocoder\Facades\Geocoder;

class CheckinTest extends TestCase
{
	use RefreshDatabase, WithFaker;

    function setUp()
    {
        parent::setUp();

        // $this->withoutEvents();

        $this->faker = $this->makeFaker('en_PH');

        $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);
    }

    /** @test */
    function checkin_can_autopopulate_location()
    {
        $longitude = 121.17332;
        $latitude = 13.928264;
        $checkin = factory(Checkin::class)->create(compact('longitude', 'latitude'));
        $messenger_id = $checkin->messenger->id;

        $checkin->refresh();
        $this->assertDatabaseHas('checkins', compact('messenger_id', 'longitude', 'latitude'));
        $this->assertNotNull($checkin->location);
    }
}
