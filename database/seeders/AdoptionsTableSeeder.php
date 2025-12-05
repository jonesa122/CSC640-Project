<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdoptionsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('adoptions')->insert([
            [
                'animal_id'      => 1,
                'adoption_date'  => '2025-11-01',
                'adopter_name'   => 'Abby Johnson',
                'adopter_phone'  => '555-1234',
                'adopter_email'  => 'abby.j@example.com',
                'adopter_address'=> '123 Maple St, Fort Mitchell, KY',
                'status'         => 'pending',
            ],
            [
                'animal_id'      => 2,
                'adoption_date'  => '2025-11-03',
                'adopter_name'   => 'Liam Smith',
                'adopter_phone'  => '555-5678',
                'adopter_email'  => 'liam.smith@example.com',
                'adopter_address'=> '456 Oak Ave, Fort Mitchell, KY',
                'status'         => 'approved',
            ],
            [
                'animal_id'      => 3,
                'adoption_date'  => '2025-11-05',
                'adopter_name'   => 'Emma Davis',
                'adopter_phone'  => '555-9012',
                'adopter_email'  => 'emma.d@example.com',
                'adopter_address'=> '789 Pine Rd, Fort Mitchell, KY',
                'status'         => 'pending',
            ],
        ]);
    }
}
