<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
	use RefreshDatabase, WithFaker;

	protected $classes = [	
		\App\Admin::class,
		\App\Operator::class,
		\App\Staff::class,
		\App\Subscriber::class,
		\App\Worker::class,
	];

    function setUp()
    {
        parent::setUp();

        $this->withoutEvents();

        $this->faker = $this->makeFaker('en_PH');

        $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);
    }

    /** @test */
    function child_model_is_essentially_a_user_with_a_type()
    {
    	$mobile = '09173011987';
    	$password = bcrypt('1234');
    	// $admin = \App\Admin::create(compact('mobile', 'password'));

    	// dd($admin);
    	foreach ($this->classes as $class) {
		    $descendant = factory($class)->create();
	  		$user = \App\User::find($descendant->id);

		    $this->assertEquals($user->id, $descendant->id);
		    $this->assertEquals($user->type, $class);
            $this->assertInstanceOf($class, $user);
    	}
    }
}
