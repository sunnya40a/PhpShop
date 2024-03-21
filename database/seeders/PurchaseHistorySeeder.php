<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PurchaseHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        DB::table('purchaseHistory')->insert([
            [
                'PO' => 1015,
                'Pdate' => '2022-02-10',
                'item_list' => 'DPD - 007',
                'description' => 'Testing',
                'qty' => 10,
                'category' => 'Dairy Products',
                'price' => 500.00,
                'user' => 'user',
            ],
            [
                'PO' => 1016,
                'Pdate' => '2022-02-12',
                'item_list' => 'DPD - 007',
                'description' => 'Testing',
                'qty' => 10,
                'category' => 'Dairy Products',
                'price' => 500.00,
                'user' => 'user',
            ],
            [
                'PO' => 1019,
                'Pdate' => '2022-02-10',
                'item_list' => 'DPD - 007',
                'description' => 'Testing-2',
                'qty' => 10,
                'category' => 'Dairy Products',
                'price' => 500.00,
                'user' => 'Chhabi',
            ],
            [
                'PO' => 10001,
                'Pdate' => '2022-02-15',
                'item_list' => 'DPD - 007',
                'description' => 'Testing',
                'qty' => 10,
                'category' => 'Dairy Products',
                'price' => 584.00,
                'user' => 'user',
            ],
            [
                'PO' => 10002,
                'Pdate' => '2022-08-15',
                'item_list' => 'DPD - 001',
                'description' => 'Testing',
                'qty' => 50,
                'category' => 'Dairy Products',
                'price' => 584.00,
                'user' => 'user',
            ],
            [
                'PO' => 10003,
                'Pdate' => '2023-02-15',
                'item_list' => 'DPD - 008',
                'description' => 'Testing',
                'qty' => 50,
                'category' => 'Dairy Products',
                'price' => 584.00,
                'user' => 'user',
            ],
            [
                'PO' => 10005,
                'Pdate' => '2024-01-15',
                'item_list' => 'DPD - 008',
                'description' => 'Testing',
                'qty' => 50,
                'category' => 'Dairy Products',
                'price' => 584.00,
                'user' => 'Chalise',
            ],
            [
                'PO' => 10006,
                'Pdate' => '2024-01-25',
                'item_list' => 'DPD - 008',
                'description' => 'Testing',
                'qty' => 50,
                'category' => 'Dairy Products',
                'price' => 584.00,
                'user' => 'Chalise',
            ],
            [
                'PO' => 10009,
                'Pdate' => '2024-01-26',
                'item_list' => 'DPD - 008',
                'description' => 'Testing',
                'qty' => 50,
                'category' => 'Dairy Products',
                'price' => 584.00,
                'user' => 'Chalise',
            ],
            [
                'PO' => 10120,
                'Pdate' => '2022-02-10',
                'item_list' => 'DPD - 007',
                'description' => 'Testing-2',
                'qty' => 10,
                'category' => 'Dairy Products',
                'price' => 59.00,
                'user' => 'Chhabi',
            ],
            [
                'PO' => 10121,
                'Pdate' => '2022-02-10',
                'item_list' => 'DPD - 007',
                'description' => 'Testing-2',
                'qty' => 10,
                'category' => 'Dairy Products',
                'price' => 59.00,
                'user' => 'Chhabi',
            ],
            [
                'PO' => 10122,
                'Pdate' => '2024-02-10',
                'item_list' => 'DPD - 007',
                'description' => 'Testing-2',
                'qty' => 10,
                'category' => 'Dairy Products',
                'price' => 59.00,
                'user' => 'Chhabi',
            ],
            [
                'PO' => 10123,
                'Pdate' => '2024-02-10',
                'item_list' => 'DPD - 007',
                'description' => 'Testing-2',
                'qty' => 1,
                'category' => 'Dairy Products',
                'price' => 59.00,
                'user' => 'Chhabi',
            ],
            [
                'PO' => 10124,
                'Pdate' => '2024-02-21',
                'item_list' => 'DPD - 007',
                'description' => 'Testing-2',
                'qty' => 1,
                'category' => 'Dairy Products',
                'price' => 59.00,
                'user' => 'Chhabi',
            ],
            [
                'PO' => 10125,
                'Pdate' => '2024-02-21',
                'item_list' => 'DPD - 007',
                'description' => 'Testing-2',
                'qty' => 1,
                'category' => 'Dairy Products',
                'price' => 59.00,
                'user' => 'Chhabi',
            ],
            [
                'PO' => 10126,
                'Pdate' => '2024-02-21',
                'item_list' => 'DPD - 007',
                'description' => 'Testing-2',
                'qty' => 27,
                'category' => 'Dairy Products',
                'price' => 59.00,
                'user' => 'Chhabi',
            ],
            [
                'PO' => 10127,
                'Pdate' => '2024-02-21',
                'item_list' => 'DPD - 007',
                'description' => 'Testing-2',
                'qty' => 27,
                'category' => 'Dairy Products',
                'price' => 59.00,
                'user' => 'Chhabi',
            ],
            [
                'PO' => 10128,
                'Pdate' => '2024-02-21',
                'item_list' => 'DPD - 007',
                'description' => 'Testing-2',
                'qty' => 27,
                'category' => 'Dairy Products',
                'price' => 59.00,
                'user' => 'Chhabi',
            ],
            [
                'PO' => 10129,
                'Pdate' => '2024-02-21',
                'item_list' => 'DPD - 007',
                'description' => 'Testing-2',
                'qty' => 27,
                'category' => 'Dairy Products',
                'price' => 59.00,
                'user' => 'Chhabi',
            ],
            [
                'PO' => 10130,
                'Pdate' => '2024-02-21',
                'item_list' => 'DPD - 007',
                'description' => 'Testing-2',
                'qty' => 27,
                'category' => 'Dairy Products',
                'price' => 59.00,
                'user' => 'Chhabi',
            ],



        ]);
    }
}
