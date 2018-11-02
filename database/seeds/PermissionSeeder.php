<?php

use App\Enum\Permission as Permissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // DB::table('permissions')->truncate();
        DB::table('permissions')->delete();
        
        $permissions = array_values(Permissions::toArray());
        foreach ($permissions as $name ) {
            Permission::create(compact('name'));            
        }
    }
}
