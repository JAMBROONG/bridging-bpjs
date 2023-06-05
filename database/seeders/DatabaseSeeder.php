<?php

namespace Database\Seeders;

use App\Models\DataPendapatanRsRi;
use App\Models\Invoices;
use App\Models\JenisJasaAkun;
use App\Models\KategoriPendapatan;
use App\Models\KpiKategori;
use App\Models\Subscribe;
use App\Models\TemplateKelasTarif;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Membuat contoh data untuk tabel 'users'
        $users = [
            [
                'name' => 'John Doe',
                'direktur' => 'John Direktur',
                'tanggal_pendaftaran' => '2023-05-16',
                'alamat' => 'Jl. Contoh Alamat',
                'telepon' => '081234567890',
                'logo' => 'logo.png',
                'website' => 'https://www.example.com',
                'kepemilikan' => 'Contoh Kepemilikan',
                'luas_tanah' => '1000 m²',
                'luas_bangunan' => '500 m²',
                'kelas' => 'A',
                'status_blu' => 'BLUD',
                'email' => 'john@example.com',
                'npwp' => '1234567890',
                'akte_pendirian' => 'akte.pdf',
                'surat_izin_usaha' => 'izin.pdf',
                'nomor_registrasi_bpjs' => '1234567890',
                'klasifikasi_lapangan_usaha' => '12345',
                'password' => Hash::make('password'),
            ],
            // Tambahkan data lainnya jika diperlukan
        ];
        // Memasukkan data ke dalam tabel 'users'
        foreach ($users as $user) {
            User::create($user);
        }
        // Membuat contoh data untuk tabel 'data_pendapatan_rs_ris'
        $dataPendapatan = [
            [
                'rm' => 123456,
                'notrans' => 'ABC123',
                'tanggal' => '2023-05-16',
                'pasien' => 'John Doe',
                'unit' => 'Unit A',
                'faktur' => 'Faktur 123',
                'produk' => 'Produk A',
                'obat' => 'Obat A',
                'qty' => '10',
                'tarip' => '100000',
                'jumlah' => '1000000',
                'dokter' => 'Dr. Smith',
                'penjamin' => 'Penjamin A',
                'invoice' => 'Invoice 123',
                'bayar' => 'Bayar 123',
                'kategori_layanan' => 'JP',
                'klasifikasi' => 'obat',
                'users_id' => 1,
            ],
            // Tambahkan data lainnya jika diperlukan
        ];

        // Memasukkan data ke dalam tabel 'data_pendapatan_rs_ris'
        foreach ($dataPendapatan as $data) {
            DataPendapatanRsRi::create($data);
        }
        $subscriptions = [
            [
                'user_id' => 1,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonth(),
                'payment_status' => 'paid',
            ],
            [
                'user_id' => 1,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonth(),
                'payment_status' => 'unpaid',
            ],
            // Tambahkan data langganan lainnya jika diperlukan
        ];
        foreach ($subscriptions as $subscription) {
            Subscribe::create($subscription);
        }

        // Seeder untuk tabel "Invoices"
        $invoices = [
            [
                'subscribes_id' => 1,
                'invoice_number' => 'INV-001',
                'amount' => 100.00,
                'payment_status' => 'paid',
                'payment_date' => Carbon::now(),
                'payment_method' => 'Mitrans',
            ],
            [
                'subscribes_id' => 1,
                'invoice_number' => 'INV-002',
                'amount' => 150.00,
                'payment_status' => 'unpaid',
                'payment_date' =>  Carbon::now(),
                'payment_method' => 'Mitrans',
            ],
            // Tambahkan data invoice lainnya jika diperlukan
        ];
        foreach ($invoices as $invoice) {
            Invoices::create($invoice);
        }

        $data = [
            [
                'id' => 1,
                'kategori' => 'Jasa Penunjang medis dan Jasa Tenaga Ahli',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'kategori' => 'Obat dan Perlengkapan Medis',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'kategori' => 'Kamar Rawat Inap',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'kategori' => 'Fasilitas Rumah Sakit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'kategori' => 'Kamar Operasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'kategori' => 'Pendapatan Administrasi dan Lainnya',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        KategoriPendapatan::insert($data);

        $kelas_tarif_khanza = [
            ['akun' => 'Administrasi', 'jenis_jasa' => 'JS', 'kategori_pendapatans_id' => 6],
            ['akun' => 'Administrasi Rawat Inap', 'jenis_jasa' => 'JS', 'kategori_pendapatans_id' => 3],
            ['akun' => 'Akomodasi', 'jenis_jasa' => 'JS', 'kategori_pendapatans_id' => 4],
            ['akun' => 'Pemeriksaan/Konsultasi Dokter', 'jenis_jasa' => 'JP', 'kategori_pendapatans_id' => 1],
            ['akun' => 'Pengambilan Sampel Darah', 'jenis_jasa' => 'JS', 'kategori_pendapatans_id' => 6],
            ['akun' => 'Tindakan Dokter', 'jenis_jasa' => 'JP', 'kategori_pendapatans_id' => 1],
            ['akun' => 'Tindakan Keperawatan', 'jenis_jasa' => 'JP', 'kategori_pendapatans_id' => 3],
            ['akun' => 'Pemeriksaan Lab', 'jenis_jasa' => 'JS', 'kategori_pendapatans_id' => 4],
            ['akun' => 'Pemeriksaan Radiologi', 'jenis_jasa' => 'JS', 'kategori_pendapatans_id' => 4],
            ['akun' => 'Operasi', 'jenis_jasa' => 'JP', 'kategori_pendapatans_id' => 5],
            ['akun' => 'Obat & BHP', 'jenis_jasa' => 'JS', 'kategori_pendapatans_id' => 2],
        ];

        $data = [];

        foreach ($kelas_tarif_khanza as $kelas_tarif) {
            $data[] = [
                'kelas_tarif' => $kelas_tarif['akun'],
                'template' => 'Khanza',
                'jenis_jasa' => $kelas_tarif['jenis_jasa'],
                'kategori_pendapatans_id' => $kelas_tarif['kategori_pendapatans_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        TemplateKelasTarif::insert($data);


        $kategori = [
            'Indeks Dasar (Basic Index)',
            'Indeks Kompetensi dan Kualifikasi',
            'Indeks Resiko',
            'Indeks Emergensi',
            'Indeks Posisi',
            'Indeks Kinerja dan Disiplin',
        ];

        $data = [];

        foreach ($kategori as $item) {
            $data[] = [
                'kategori' => $item,
            ];
        }

        KpiKategori::insert($data);

        $data = [
            [
                'id' => 12,
                'user_id' => 1,
                'kelas_tarif' => 'Ambulance',
                'jenis_jasa' => 'JS',
                'kategori_pendapatans_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 13,
                'user_id' => 1,
                'kelas_tarif' => 'BMHP',
                'jenis_jasa' => 'JS',
                'kategori_pendapatans_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 14,
                'user_id' => 1,
                'kelas_tarif' => 'Fisioterapi',
                'jenis_jasa' => 'JS',
                'kategori_pendapatans_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 15,
                'user_id' => 1,
                'kelas_tarif' => 'Instalasi Bedah Sentral',
                'jenis_jasa' => 'JS',
                'kategori_pendapatans_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 16,
                'user_id' => 1,
                'kelas_tarif' => 'Instalasi Gawat Darurat',
                'jenis_jasa' => 'JS',
                'kategori_pendapatans_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 17,
                'user_id' => 1,
                'kelas_tarif' => 'Jasa Pelayanan',
                'jenis_jasa' => 'JP',
                'kategori_pendapatans_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 18,
                'user_id' => 1,
                'kelas_tarif' => 'Laboratorium',
                'jenis_jasa' => 'JS',
                'kategori_pendapatans_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 19,
                'user_id' => 1,
                'kelas_tarif' => 'Pemakaian Obat dan Alkes',
                'jenis_jasa' => 'JS',
                'kategori_pendapatans_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 20,
                'user_id' => 1,
                'kelas_tarif' => 'Prosedur Non Bedah',
                'jenis_jasa' => 'JS',
                'kategori_pendapatans_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 21,
                'user_id' => 1,
                'kelas_tarif' => 'Radiologi',
                'jenis_jasa' => 'JS',
                'kategori_pendapatans_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 22,
                'user_id' => 1,
                'kelas_tarif' => 'Ruang Perawatan',
                'jenis_jasa' => 'JS',
                'kategori_pendapatans_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 23,
                'user_id' => 1,
                'kelas_tarif' => 'Sewa Alat',
                'jenis_jasa' => 'JS',
                'kategori_pendapatans_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 24,
                'user_id' => 1,
                'kelas_tarif' => 'Tindakan Dokter',
                'jenis_jasa' => 'JP',
                'kategori_pendapatans_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 25,
                'user_id' => 1,
                'kelas_tarif' => 'Tindakan Medis Perawatan',
                'jenis_jasa' => 'JP',
                'kategori_pendapatans_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 26,
                'user_id' => 1,
                'kelas_tarif' => 'Tunai',
                'jenis_jasa' => 'JS',
                'kategori_pendapatans_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 27,
                'user_id' => 1,
                'kelas_tarif' => 'Visite/Konsul Dokter',
                'jenis_jasa' => 'JP',
                'kategori_pendapatans_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        JenisJasaAkun::insert($data);
    }
}
