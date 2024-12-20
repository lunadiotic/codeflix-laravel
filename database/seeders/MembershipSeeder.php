<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan foreign key checks sementara
        Schema::disableForeignKeyConstraints();

        // Kosongkan tabel sebelum melakukan seed
        DB::table('memberships')->truncate();

        // Data membership
        $memberships = [
            [
                'user_id' => 1,
                'plan_id' => 1, // Basic Plan
                'active' => true,
                'start_date' => now()->subDays(10)->format('Y-m-d'),
                'end_date' => now()->addDays(20)->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'plan_id' => 2, // Standard Plan
                'active' => true,
                'start_date' => now()->subDays(5)->format('Y-m-d'),
                'end_date' => now()->addDays(25)->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'plan_id' => 3, // Premium Plan
                'active' => false,
                'start_date' => now()->subDays(40)->format('Y-m-d'),
                'end_date' => now()->subDays(10)->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'plan_id' => 1, // Basic Plan
                'active' => true,
                'start_date' => now()->subDays(1)->format('Y-m-d'),
                'end_date' => now()->addDays(29)->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'plan_id' => 2, // Standard Plan
                'active' => false,
                'start_date' => now()->subDays(60)->format('Y-m-d'),
                'end_date' => now()->subDays(30)->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Masukkan data ke tabel memberships
        DB::table('memberships')->insert($memberships);

        // Aktifkan kembali foreign key checks
        Schema::enableForeignKeyConstraints();
    }
}