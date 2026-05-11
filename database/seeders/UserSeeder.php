<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            [
                'name'          => 'সাঈদ',
                'email'         => 'sayeed@admin.com',
                'password'      => Hash::make('admin1234'),
                'timezone'      => 'Asia/Colombo',
                'art_start_date'=> '2026-05-07',
                'tb_start_date' => '2026-05-04',
            ]
        );
    }
}
