<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
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
                'password' => '$2a$06$paKYSf0ex0xVhH4bNVThN.zUkvMzHNgZHztkkonecpCyzkAH4DW9.',
                'comment' => 'Sambhav.40a',
            ],
            [
                'name' => 'chhabi',
                'email' => 'chhabi40a@gmail.com',
                'role' => 'user',
                'password' => '$2a$06$QEfcVeTRuu7DSq9WUhS1Cu2uKb5vuqhJwRSVc5kmh2RKJulNQUTvq',
                'comment' => 'Chhabi@40a',
            ],
            [
                'name' => 'avi',
                'email' => 'avi@gmail.com',
                'role' => 'user',
                'password' => '$2a$06$sNh0TpofxBKdf29hLSkLrue5Q2W8Y.EwVxxfujwNLbjebNT4ki/AK',
                'comment' => 'AviDevkota@1974',
            ],
            [
                'name' => 'Test',
                'email' => 'test@gmail.com',
                'role' => 'user',
                'password' => '$2a$13$EDSGdItsSRs/G5HA8zI3ye0zCT3sHBEb3ZFlgUpd/scUp.Ox9b.vm',
                'comment' => 'Password!"#$%&\'()*+,-./:;<=>?@^_{|}~',
            ],
            //'password' => Hash::make('secretpassword'),
        ]);
    }
}