<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->updateOrInsert(
            ['username' => 'aldmic'],
            [
                'name'       => 'Aldmic',
                'username'   => 'aldmic',
                'email'      => 'aldmic@example.com',
                'password'   => Hash::make('123abc123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
