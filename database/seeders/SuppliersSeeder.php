<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuppliersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('suppliers')->insert([
            [
                's_name' => 'supplier one',
                'mobile1' => '974',
                'mobile2' => '975',
                'c_person' => 'person',
                'contact_info' => 'contact info',
            ],
            [
                's_name' => 'Supplier Two',
                'mobile1' => '9846',
                'mobile2' => '974',
                'c_person' => '2nd Person',
                'contact_info' => 'contact info',
            ],
            [
                's_name' => 'Simle',
                'mobile1' => '4586',
                'mobile2' => '548',
                'c_person' => 'Deepak',
                'contact_info' => 'facebook id :45854',
            ],
        ]);
    }
}
