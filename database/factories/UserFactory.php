<?php

use Faker\Generator as Faker;

use Faker\Factory as FakerFactory;
/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$children = [
    \App\User::class,
    \App\Admin::class,
    \App\Operator::class,
    \App\Staff::class,
    \App\Subscriber::class,
    \App\Worker::class,
];


foreach ($children as $child) {
    $factory->define($child, function (Faker $faker) {

	    static $password;
    	
        $faker = FakerFactory::create('en_PH');

	    return [
	        'name' => $faker->username,
            'mobile' => $faker->mobileNumber,
	        'email' => $faker->unique()->safeEmail,
	        'password' => $password ?: $password = bcrypt('1234'),
            // 'authy_id' => $faker->randomNumber(7),
	        'remember_token' => str_random(10),
		    ];
    });
}