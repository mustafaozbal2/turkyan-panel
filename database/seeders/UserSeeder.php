<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            // 1. Admin Kullanıcısı
            [
                'name' => 'Admin',
                'email' => 'admin@turkyan.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 2. İtfaiye Kullanıcısı
            [
                'name' => 'İtfaiye Yetkilisi',
                'email' => 'itfaiye@turkyan.com',
                'password' => Hash::make('password'),
                'role' => 'itfaiye',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 3. Bakanlık Kullanıcısı
            [
                'name' => 'Bakanlık Yetkilisi',
                'email' => 'bakanlik@turkyan.com',
                'password' => Hash::make('password'),
                'role' => 'bakanlik',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 4. Normal Kullanıcı (Gönüllü)
            [
                'name' => 'Gönüllü Kullanıcı',
                'email' => 'user@turkyan.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}