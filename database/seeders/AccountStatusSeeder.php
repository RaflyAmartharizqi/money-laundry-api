<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('account_status')->insert([
            'name' => 'basic',
            'price' => 0,
            'range' => 0,
        ]);

        DB::table('account_status')->insert([
            'name' => 'silver',
            'price' => 50000,
            'range' => 30,
        ]);

        DB::table('account_status')->insert([
            'name' => 'gold',
            'price' => 130000,
            'range' => 90,
        ]);
    }
}
