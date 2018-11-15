<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
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
    function child_model_is_essentially_a_user_with_a_type()
    {
    	$mobile = '09173011987';
    	$password = bcrypt('1234');
    	// $admin = \App\Admin::create(compact('mobile', 'password'));

    	// dd($admin);
    	foreach (\App\User::$classes as $class) {
		    $descendant = factory($class)->create();
	  		$user = \App\User::find($descendant->id);

		    $this->assertEquals($user->id, $descendant->id);
		    $this->assertEquals($user->type, $class);
            $this->assertInstanceOf($class, $user);
    	}
    }

    /** @test */
    function user_has_schemaless_atttributes()
    {
        $key = 'color';
        $value = 'blue';

        $user = factory(\App\User::class)->create();

        $user->extra_attributes->set($key, $value);
        $user->save();

        $this->assertEquals($user->extra_attributes->get($key), $value);
        $this->assertDatabaseHas('users', [
            'extra_attributes' => json_encode([$key => $value]),
        ]);
    }

    /** @test */
    function user_has_status()
    {
        $name = 'check';
        $reason = 'test';
        $model_type = \App\User::class;
        $user = factory($model_type)->create();
        $model_id = $user->id;
        $user->setStatus($name, $reason);

        $this->assertEquals($user->status, $name);
        $this->assertDatabaseHas('statuses', compact('name', 'reason', 'model_type', 'model_id'));
    }

    /** @test */
    function user_can_have_tasks()
    {
        $user = factory(\App\User::class)->create();

        $user->syncTasks(config('chatbot.tasks.test'));

        $this->assertDatabaseHas('tasks', [
            'title' => 'Task 1',
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Task 2',
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Task 3',
            'user_id' => $user->id,
        ]);
    }    


    /** @test */
    function user_can_checkin()
    {
        $user = factory(\App\User::class)->create();
        $user_id = $user->id;
        $longitude = 4.9205266;
        $latitude = 52.3832816;

        // $user->checkins()->create(compact('longitude', 'latitude'));

        // $user->checkin($longitude, $latitude);
        $user->checkin(compact('longitude', 'latitude'));
        $this->assertDatabaseHas('checkins', compact('user_id', 'longitude', 'latitude'));
    }
}
