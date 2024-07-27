<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UserStatsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear the table before seeding
        DB::table('user_stats')->truncate();

        // Define the data to seed
        $data = [];
        //we generate a week of mock data, 7 days, 144 items per day (24 hours, each hour is 6*10 minutes)
        $startDate = Carbon::now()->subDays(7);
        $next = $startDate;
        $now = Carbon::now();
        while($next < $now){
            $data[] = [
                'online_users' => rand(1000, 1600),
                'date' => $next->format('Y-m-d'),
                'created_at' => $next->format('Y-m-d H:i:s'),
                'updated_at' => $next->format('Y-m-d H:i:s')
            ];
            $next = $startDate->addMinutes(10);
        }

        DB::table('user_stats')->insert($data);
    }
}
