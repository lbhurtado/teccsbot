<?php

use App\Messenger;
use Faker\Generator as Faker;

$factory->define(App\Checkin::class, function (Faker $faker) {
    return [
        'longitude' => $faker->longitude,
        'latitude' => $faker->latitude,
        'messenger_id' => factory(Messenger::class)->create()->id
    ];
});
