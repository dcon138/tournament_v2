<?php

use Illuminate\Database\Seeder;
use Rhumsaa\Uuid\Uuid;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'uuid' => Uuid::uuid4()->toString(),
            'first_name' => 'Administrator',
            'last_name' => 'Example',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin')
    	]);
        
        DB::table('users')->insert([
            'uuid' => Uuid::uuid4()->toString(),
            'first_name' => 'Daniel',
            'last_name' => 'Condie',
            'email' => 'daniel.condie18@gmail.com',
            'password' => bcrypt('admin')
        ]);
    }
}
