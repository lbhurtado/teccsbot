<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
    	
        $this->call(PermissionSeeder::class);
		$this->call(AdminSeeder::class);
        $this->call(PlacementSeeder::class);

        Schema::enableForeignKeyConstraints();
    }
}
