<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
                'category' => 'Dairy Products',
            ],
            [
                'item_list' => 'DPD - 002',
                'description' => 'Cow Milk',
                'qty' => 20,
                'category' => 'Dairy Products',
            ],
            [
                'item_list' => 'DPD - 007',
                'description' => 'Testing',
                'qty' => 208,
                'category' => 'Dairy Products',
            ],
            [
                'item_list' => 'DPD - 008',
                'description' => 'Testing',
                'qty' => 250,
                'category' => 'Dairy Products',
            ],
        ]);
    }
}
