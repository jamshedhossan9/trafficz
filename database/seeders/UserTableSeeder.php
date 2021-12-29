<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('users')->truncate();

        $data = [
            ['id' => 1, 'name' => 'Super Admin', 'email' => 'superadmin@example.com', 'password' => Hash::make('123456'), 'status' => 1, 'created_at' => Carbon::now()],
            ['id' => 2, 'name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('123456'), 'status' => 1, 'created_at' => Carbon::now()],
            ['id' => 3, 'name' => 'User', 'email' => 'user@example.com', 'password' => Hash::make('123456'), 'status' => 1, 'created_at' => Carbon::now()],
        ];
        DB::table('users')->insert($data);

        $this->assignDefaultRoleToUsers();

    }

    private function assignDefaultRoleToUsers(){
        $superAdmin =  User::find(1);
        $superAdmin->roles()->attach(1);

        $admin =  User::find(2);
        $admin->roles()->attach(2);

        $user =  User::find(3);
        $user->roles()->attach(3);
    }
}
