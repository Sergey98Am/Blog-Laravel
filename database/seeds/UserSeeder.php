<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'Sergey Gabrielyan',
                'email' => 'serg98barca@gmail.com',
                'password' => Hash::make('serg_password'),
                'role_id' => 2,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'Aram Vardanyan',
                'email' => 'aram@gmail.com',
                'password' => Hash::make('aramaram'),
                'role_id' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
        ];

        User::insert($users);
    }
}
