<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
/**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'sambhav',
                'email' => 'sambhav40a@gmail.com',
                'role' => 'user',
                'password' => Hash::make('Sambhav^40a'),
                'comment' => 'Sambhav.40a',
            ],
            [
                'name' => 'chhabi',
                'email' => 'chhabi40a@gmail.com',
                'role' => 'user',
                'password' => Hash::make('Chhabi@40a'),
                'comment' => 'Chhabi@40a',
            ],
            [
                'name' => 'avi',
                'email' => 'avi@gmail.com',
                'role' => 'user',
                'password' => Hash::make('Avi@Devkota'),
                'comment' => 'AviDevkota@1974',
            ],
            [
                'name' => 'Test',
                'email' => 'test@gmail.com',
                'role' => 'user',
                'password' => Hash::make('Test@123'),
                'comment' => 'Password!"#$%&\'()*+,-./:;<=>?@^_{|}~',
            ],
            //'password' => Hash::make('secretpassword'),
        ]);
    }
}
