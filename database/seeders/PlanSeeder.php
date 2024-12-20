<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan foreign key checks sementara
        Schema::disableForeignKeyConstraints();

        // Kosongkan tabel sebelum melakukan seed
        DB::table('plans')->truncate();

        // Data rencana langganan
        $plans = [
            [
                'title' => 'Basic',
                'price' => 49999, // Harga dalam satuan terkecil (contoh: IDR)
                'resolution' => '720p',
                'max_devices' => 1,
                'duration' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Standard',
                'price' => 89999,
                'resolution' => '1080p',
                'max_devices' => 2,
                'duration' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Premium',
                'price' => 129999,
                'resolution' => '4k',
                'max_devices' => 4,
                'duration' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        // Masukkan data ke tabel plans
        DB::table('plans')->insert($plans);

        // Aktifkan kembali foreign key checks
        Schema::enableForeignKeyConstraints();
    }
}