<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\DataPendapatanRsRi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        //     'password' => Hash::make("inipassword"),
        // ]);
        // \App\Models\DataPendapatanRsRi::factory()->count(10)->create([
        //     'rm' => rand(1000, 9999),
        //     'notrans' => 'TRX' . rand(1000, 9999),
        //     'tanggal' => now(),
        //     'pasien' => 'Pasien ' . rand(1, 10),
        //     'unit' => 'Unit ' . rand(1, 5),
        //     'faktur' => 'Faktur ' . rand(1, 10),
        //     'produk' => 'Produk ' . rand(1, 10),
        //     'obat' => 'Obat ' . rand(1, 10),
        //     'qty' => rand(1, 10),
        //     'tarip' => rand(10000, 99999),
        //     'jumlah' => rand(10000, 99999),
        //     'dokter' => 'Dokter ' . rand(1, 5),
        //     'penjamin' => 'Penjamin ' . rand(1, 5),
        //     'invoice' => 'Invoice ' . rand(1000, 9999),
        //     'bayar' => 'Bayar ' . rand(1000, 9999),
        //     'jenis_layanan' => 'RI',
        //     'kategori_layanan' => 'JP',
        //     'klasifikasi' => 'obat',
        //     'users_id' => 1, // Ganti dengan user ID yang sesuai
        // ]);
    }
}
