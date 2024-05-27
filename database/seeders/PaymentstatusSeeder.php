<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentstatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('paymentstatuses')->insert([
            [
                'code' => 1,
                'status' => 'Paid (Cash)',
                'onpurchase' => true,
                'onsale' => true,
            ],
            [
                'code' => 1,
                'status' => 'Paid (Bank)',
                'onpurchase' => true,
                'onsale' => true,
            ],
            // [
            //     'code' => 2,
            //     'status' => 'Unpaid',
            //     'onpurchase' => false,
            //     'onsale' => true,
            // ],
            [
                'code' => 2,
                'status' => 'On Credit',
                'onpurchase' => true,
                'onsale' => true,
            ],
            [
                'code' => 3,
                'status' => 'Due',
                'onpurchase' => false,
                'onsale' => true,
            ],
            [
                'code' => 4,
                'status' => 'Overdue',
                'onpurchase' => false,
                'onsale' => true,
            ],
        ]);
    }
}
