<?php

use App\{User, Admin, Placement};
use Illuminate\Database\Seeder;

class PlacementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('placements')->truncate();

        $admin = Admin::first();

        foreach(User::$classes as $key => $values) {
			$code = $key;
			$type = $values;
            $message = env('BOT_REGISTRATION_MESSAGE_'.strtoupper($key), 'You are now a registered '.strtolower($key).'.');
			Placement::record(compact('code', 'type', 'message'), $admin);  	
        }
    }
}
