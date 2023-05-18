<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Invoices;
use App\Models\Subscribe;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;


class App extends Controller
{
    public function index()
    {
        $userId = Auth::id(); // Mendapatkan ID user yang sedang login
        return Inertia::render('Dashboard', []);
    }
    public function uploadShifting(Request $request)
    {
        $request->validate([
            'file1' => 'required|file|mimes:xlsx,xls',
            'file2' => 'required|file|mimes:xlsx,xls',
        ]);
        $file1 = $request->file('file1');
        $file2 = $request->file('file2');
        $path1 = $file1->store('excels');
        $path2 = $file2->store('excels');
        
        // Membaca file pertama
    $rows1 = Excel::toArray([], $path1);
    $data1 = [];
    $headers1 = $rows1[0][0]; // Ambil baris pertama sebagai header
    foreach ($rows1[0] as $index => $row) {
        if ($index === 0) {
            continue; // Skip the header row
        }

        $data = [];
        foreach ($row as $key => $value) {
            switch ($headers1[$key]) {
                case 'RM':
                    $data['rm'] = $value;
                    break;
                case 'NOTRANS':
                    $data['no_transaksi'] = $value;
                    break;
                case 'TANGGAL':
                    $data['tanggal'] = $value;
                    break;
                case 'PASIEN':
                    $data['pasien'] = $value;
                    break;
                case 'UNIT':
                    $data['unit'] = $value;
                    break;
                case 'FAKTUR':
                    $data['faktur'] = $value;
                    break;
                case 'PRODUK':
                    $data['produk'] = $value;
                    break;
                case 'KLS TARIF':
                    $data['kls_tarif'] = $value;
                    break;
                case 'OBAT':
                    $data['obat'] = $value;
                    break;
                case 'QTY':
                    $data['qty'] = $value;
                    break;
                case 'TARIP':
                    $data['tarip'] = $value;
                    break;
                case 'JUMLAH':
                    $data['jumlah'] = $value;
                    break;
                case 'DOKTER':
                    $data['dokter'] = $value;
                    break;
                case 'PENJAMIN':
                    $data['penjamin'] = $value;
                    break;
                case 'INVOICE':
                    $data['invoice'] = $value;
                    break;
                case 'BAYAR':
                    $data['bayar'] = $value;
                    break;
                default:
                    // Nilai tidak sesuai dengan patokan, tidak dilakukan apa-apa atau bisa dimasukkan ke dalam string kosong
                    break;
            }            
        }
        $data1[] = $data;
    }

    // Membaca file kedua
    $rows2 = Excel::toArray([], $path2);
    $data2 = [];
    $headers2 = $rows2[0][0]; // Ambil baris pertama sebagai header
    foreach ($rows2[0] as $index => $row) {
        if ($index === 0) {
            continue; // Skip the header row
        }

        $data = [];
        foreach ($row as $key => $value) {
            if ($headers2[$key] === 'Nama Obat') {
                $data['nama_obat'] = $value;
            } elseif ($headers2[$key] === 'Harga Beli') {
                $data['harga_beli'] = $value;
            }
        }
        $data2[] = $data;
    }

    // Menggabungkan data dari file pertama dan kedua
    // $mergedData = array_merge($data1, $data2);


    Storage::delete($path1,$path2);
        $response = [
            'data' => $data1,
        ];

        return response()->json($response);
    }
}
