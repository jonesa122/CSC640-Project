<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnimalsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('animals')->insert([
            [
                'name'         => 'Bella',
                'species'      => 'Dog',
                'breed'        => 'Labrador Retriever',
                'age'          => 3,
                'gender'       => 'Female',
                'arrival_date' => '2025-10-01',
                'status'       => 'Available',
            ],
            [
                'name'         => 'Max',
                'species'      => 'Cat',
                'breed'        => 'Siamese',
                'age'          => 2,
                'gender'       => 'Male',
                'arrival_date' => '2025-09-15',
                'status'       => 'Fostered',
            ],
            [
                'name'         => 'Charlie',
                'species'      => 'Rabbit',
                'breed'        => 'Dutch',
                'age'          => 1,
                'gender'       => 'Male',
                'arrival_date' => '2025-10-20',
                'status'       => 'Available',
            ],
        ]);
    }
}
