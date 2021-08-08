<?php

use Illuminate\Database\Seeder;
use App\User;
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([

            'name' => 'Rishabh',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456'),
            'user_status'=> '1',
        ]);
    }
}
