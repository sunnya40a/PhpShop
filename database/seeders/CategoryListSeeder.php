<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CategoryListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
            DB::table('category_list')->insert([
            ['category_code' => 'BCR', 'description' => 'Baby Care'],
            ['category_code' => 'BAK', 'description' => 'Bakery'],
            ['category_code' => 'BEV', 'description' => 'Beverages'],
            ['category_code' => 'CHO', 'description' => 'Chocolates'],
            ['category_code' => 'CSP', 'description' => 'Cleaning Supplies'],
            ['category_code' => 'DPD', 'description' => 'Dairy Products'],
            ['category_code' => 'FDE', 'description' => 'Frozen Desserts'],
            ['category_code' => 'ILO', 'description' => 'Ice Lollies'],
            ['category_code' => 'RCC', 'description' => 'Mobile Recharge Cards'],
            ['category_code' => 'PHY', 'description' => 'Personal Hygiene'],
            ['category_code' => 'SNK', 'description' => 'Snacks'],
            ['category_code' => 'SWT', 'description' => 'Sweets'],
        ]);
    }
}
