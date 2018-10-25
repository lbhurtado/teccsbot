<?php

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->truncate();

        $mobile = env('ADMIN_MOBILE', decrypt(
        	'eyJpdiI6InBNQzljQVZiaUd4Vk1LUmlcL1orMG5BPT0iLCJ2YWx1ZSI6IkFxRnZLWnI2RlpaWXNFU2hGV3N5S0hKdUgzSEtlWVFIQTJqd1dDcnNSMGs9IiwibWFjIjoiNzEzM2ZlZmI2NTE2ZWFiY2Y5MWMzYzYyMjc5ODRmMGVhOTgxZDcyNGJlMTYzYmM5NmY1ZWMzYjYxMjcwZDliNSJ9'));
        $name = env('ADMIN_NAME', decrypt(
        	'eyJpdiI6InZkRUpOYXdoSENKOTkzYTBBYmoyVnc9PSIsInZhbHVlIjoibDQyRWFIcmVEWWEwcmhjdGhualdxQ2c1ZE5CTVB5NzlUTFdieWN2SjFRdz0iLCJtYWMiOiIxMTMwZmRlYzFkZjhlMGE2NjA1NzUyMjFiNmI3NDQ5NjNhMjdmY2U1YTdiMzk2ODVjNGQ0YzVkNDliNjY3OTllIn0='));
        $password = '$2y$10$gz7MXG5YLhKykthNDjkfWu.fV80v.WpS..xn3T5SOza2Vo7tfGHtG';
        $authy_id = '7952368';
        \App\Admin::create(compact('mobile', 'name', 'password', 'authy_id'))
            ;
    }
}
