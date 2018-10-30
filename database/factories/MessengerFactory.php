<?php

use Faker\Generator as Faker;

$factory->define(App\Messenger::class, function (Faker $faker) {
    return [
    	'driver' => 'Web',
        'channel_id' => $faker->randomNumber(8),
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
    ];
});