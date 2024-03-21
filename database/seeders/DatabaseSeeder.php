<?php

// namespace Database\Seeders;

// // use Illuminate\Database\Console\Seeds\WithoutModelEvents;
// use Illuminate\Database\Seeder;

// class DatabaseSeeder extends Seeder
// {
//     /**
//      * Seed the application's database.
//      */
//     public function run(): void
//     {
//         $this->call([
//             UserSeeder::class,
//             //CategoryListSeeder::class,
//             //PurchaseHistorySeeder::class,
//             //InventorySeeder::class,
//         ]);
//     }
// }

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            CategoryListSeeder::class,
            UserSeeder::class,
            PurchaseHistorySeeder::class,
            InventorySeeder::class,
        ]);

    }
}