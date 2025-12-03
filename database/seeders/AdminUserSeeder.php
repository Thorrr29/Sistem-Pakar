<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat akun admin default untuk mengakses dashboard.
        // Pastikan untuk mengganti email/password ini pada lingkungan produksi.
        User::firstOrCreate(
            ['email' => 'admin@sispak-gigi.test'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('password'),
                'is_admin' => true,
            ]
        );
    }
}
