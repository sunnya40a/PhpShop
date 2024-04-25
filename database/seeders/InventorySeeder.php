<?php

namespace Database\Seeders;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('inventory')->insert([
            [
                'item_list' => 'DPD - 001',
                'description' => 'Milk (Shore)',
                'qty' => 54,
                'unit' => 'LTR',
                'category' => 'Dairy Products',
            ],
            [
                'item_list' => 'DPD - 002',
                'description' => 'Cow Milk',
                'qty' => 20,
                'unit' => 'LTR',
                'category' => 'Dairy Products',
            ],
            [
                'item_list' => 'DPD - 007',
                'description' => 'Testing',
                'qty' => 208,
                'unit' => 'LTR',
                'category' => 'Dairy Products',
            ],
            [
                'item_list' => 'DPD - 008',
                'description' => 'Testing',
                'qty' => 250,
                'unit' => 'LTR',
                'category' => 'Dairy Products',
            ],
        ]);
    }
}
