<?php

namespace App\Http\Controllers;

use App\Models\PercentageJsJp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class App extends Controller
{
    public function index()
    {
        $userId = Auth::id(); // Mendapatkan ID user yang sedang login
        $data = PercentageJsJp::where('user_id', $userId)->get();
        return Inertia::render('Dashboard', [
            'percentage' => $data
        ]);
    }
    public function uploadShifting(Request $request)
    {
        ini_set('memory_limit', '1024M');

        $request->validate([
            'file1' => 'required|file|mimes:xlsx,xls',
            'file2' => 'required|file|mimes:xlsx,xls',
        ]);
        $file1 = $request->file('file1');
        $file2 = $request->file('file2');
        $path1 = $file1->store('excels');
        $path2 = $file2->store('excels');
        $layanan = $request->input('layanan');
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
                switch ($headers2[$key]) {
                    case 'No.':
                        $data['no'] = $value;
                        break;
                    case 'Tgl. Masuk':
                        $data['tgl_masuk'] = $value;
                        break;
                    case 'Tgl. Pulang':
                        $data['tgl_pulang'] = $value;
                        break;
                    case 'No. RM':
                        $data['no_rm'] = $value;
                        break;
                    case 'Nama Pasien':
                        $data['nama_pasien'] = $value;
                        break;
                    case 'No. Klaim / SEP':
                        $data['no_klaim_sep'] = $value;
                        break;
                    case 'INACBG':
                        $data['inacbg'] = $value;
                        break;
                    case 'Top Up':
                        $data['top_up'] = $value;
                        break;
                    case 'Total Tarif':
                        $data['total_tarif'] = $value;
                        break;
                    case 'Tarif RS':
                        $data['tarif_rs'] = $value;
                        break;
                    case 'Jenis':
                        $data['jenis'] = $value;
                        break;
                    default:
                        break;
                }
            }
            $data2[] = $data;
        }
        $data1 = $this->readDataFromFirstFile($path1);
        $data2 = $this->readDataFromSecondFile($path2);

        $array_BPJS = [];
        $array_NoBPJS = [];

        foreach ($data1 as $data) {
            if ($data['penjamin'] === 'BPJS') {
                $array_BPJS[] = $data;
            } else if ($data['penjamin'] === 'UMUM') {
                $array_NoBPJS[] = $data;
            }
        }

        usort($data1, function ($a, $b) {
            return strcmp($a['rm'], $b['rm']);
        });
        usort($array_NoBPJS, function ($a, $b) {
            return strcmp($a['pasien'], $b['pasien']);
        });
        
        usort($data2, function ($a, $b) {
            return $a['no_rm'] - $b['no_rm'];
        });
        $response = [
            'dataRsBpjs' => $array_BPJS,
            'dataRsNoBpjs' => $array_NoBPJS,
            'dataBpjs' => $data2,
            'Jenis Layanan' => $layanan,
        ];
        return response()->json($response);
    }
    private function readDataFromSecondFile($filePath)
    {
        $rows = Excel::toArray([], $filePath);
        $data = [];
        $headers = $rows[0][0]; // Ambil baris pertama sebagai header

        foreach ($rows[0] as $index => $row) {
            if ($index === 0) {
                continue; // Skip the header row
            }

            $rowData = [];

            foreach ($row as $key => $value) {
                switch ($headers[$key]) {
                    case 'No.':
                        $rowData['no'] = $value;
                        break;
                    case 'Tgl. Masuk':
                        $rowData['tgl_masuk'] = $value;
                        break;
                    case 'Tgl. Pulang':
                        $rowData['tgl_pulang'] = $value;
                        break;
                    case 'No. RM':
                        $rowData['no_rm'] = $value;
                        break;
                    case 'Nama Pasien':
                        $rowData['nama_pasien'] = $value;
                        break;
                    case 'No. Klaim / SEP':
                        $rowData['no_klaim_sep'] = $value;
                        break;
                    case 'INACBG':
                        $rowData['inacbg'] = $value;
                        break;
                    case 'Top Up':
                        $rowData['top_up'] = $value;
                        break;
                    case 'Total Tarif':
                        $rowData['total_tarif'] = $value;
                        break;
                    case 'Tarif RS':
                        $rowData['tarif_rs'] = $value;
                        break;
                    case 'Jenis':
                        $rowData['jenis'] = $value;
                        break;
                    default:
                        break;
                }
            }

            $data[] = $rowData;
        }
        return $data;
    }

    private function readDataFromFirstFile($filePath)
    {
        $rows = Excel::toArray([], $filePath);
        $data = [];
        $headers = $rows[0][0]; // Ambil baris pertama sebagai header

        foreach ($rows[0] as $index => $row) {
            if ($index === 0) {
                continue;
            }

            $rowData = [];
            foreach ($row as $key => $value) {
                switch ($headers[$key]) {
                    case 'RM':
                        $rowData['rm'] = $value;
                        break;
                    case 'NOTRANS':
                        $rowData['no_transaksi'] = $value;
                        break;
                    case 'TANGGAL':
                        $rowData['tanggal'] = $value;
                        break;
                    case 'PASIEN':
                        $rowData['pasien'] = $value;
                        break;
                    case 'UNIT':
                        $rowData['unit'] = $value;
                        break;
                    case 'FAKTUR':
                        $rowData['faktur'] = $value;
                        break;
                    case 'PRODUK':
                        $rowData['produk'] = $value;
                        break;
                    case 'KLS TARIF':
                        $rowData['kls_tarif'] = $value;
                        break;
                    case 'OBAT':
                        $rowData['obat'] = $value;
                        break;
                    case 'QTY':
                        $rowData['qty'] = $value;
                        break;
                    case 'TARIP':
                        $rowData['tarip'] = $value;
                        break;
                    case 'JUMLAH':
                        $rowData['jumlah'] = $value;
                        break;
                    case 'DOKTER':
                        $rowData['dokter'] = $value;
                        break;
                    case 'PENJAMIN':
                        $rowData['penjamin'] = $value;
                        break;
                    case 'INVOICE':
                        $rowData['invoice'] = $value;
                        break;
                    case 'BAYAR':
                        $rowData['bayar'] = $value;
                        break;
                    default:
                        break;
                }
            }

            $data[] = $rowData;
        }

        return $data;
    }
}
