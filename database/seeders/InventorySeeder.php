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
                'qty' => 50,
                'unit' => 'LTR',
                'supplier_id' => 1,
                'category' => 'Dairy Products',
            ],
            [
                'item_list' => 'DPD - 002',
                'description' => 'Cow Milk',
                'qty' => 0,
                'unit' => 'LTR',
                'supplier_id' => 1,
                'category' => 'Dairy Products',
            ],
            [
                'item_list' => 'DPD - 007',
                'description' => 'Testing',
                'qty' => 218,
                'unit' => 'LTR',
                'supplier_id' => 1,
                'category' => 'Dairy Products',
            ],
            [
                'item_list' => 'DPD - 008',
                'description' => 'Testing',
                'qty' => 200,
                'unit' => 'LTR',
                'supplier_id' => 1,
                'category' => 'Dairy Products',
            ],
        ]);
    }
}
