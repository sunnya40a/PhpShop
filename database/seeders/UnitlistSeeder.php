<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitlistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('unitlists')->insert(
            [
                ['measurement' => 'Bottle', 'unit' => 'BOT'],
                ['measurement' => 'Box', 'unit' => 'BX'],
                ['measurement' => 'Can', 'unit' => 'CAN'],
                ['measurement' => 'Carton', 'unit' => 'CRT'],
                ['measurement' => 'Case', 'unit' => 'CS'],
                ['measurement' => 'Dozen', 'unit' => 'DOZ'],
                ['measurement' => 'Each/Number', 'unit' => 'EA'],
                ['measurement' => 'JAR', 'unit' => 'JAR'],
                ['measurement' => 'Kilogram', 'unit' => 'KG'],
                ['measurement' => 'Liter', 'unit' => 'LTR'],
                ['measurement' => 'Mililiter', 'unit' => 'ML'],
                ['measurement' => 'Packet', 'unit' => 'PAK'],
                ['measurement' => 'Roll', 'unit' => 'ROL'],
                ['measurement' => 'Set', 'unit' => 'SET'],
            ]
        );
    }
}
