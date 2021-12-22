<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->truncate();

        $data = [
            ['id' => 1, 'title' => 'Super Admin', 'created_at' => Carbon::now()],
            ['id' => 2, 'title' => 'Admin', 'created_at' => Carbon::now()],
            ['id' => 3, 'title' => 'User', 'created_at' => Carbon::now()]
        ];

        Role::insert($data);
    }
}
