<?php

namespace Database\Seeders;

use App\Models\Tracker;
use Illuminate\Database\Seeder;

class TrackerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tracker::create([
            'name' => 'Voluum',
            'slug' => 'voluum'
        ]);

        Tracker::create([
            'name' => 'Binom',
            'slug' => 'binom'
        ]);
    }
}
