<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\DataDokter;
use App\Models\JenisJasaAkun;
use App\Models\PercentageJlJtl;
use App\Models\PercentageJsJp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

use function GuzzleHttp\Promise\all;

class App extends Controller
{
    public function index()
    {
        Session::forget('pathPendapatanRI');
        Session::forget('pathPendapatanRJ');
        Session::forget('pathBPJSRI');
        Session::forget('pathBPJSRJ');
        Session::forget('dataPendapatanRI');
        Session::forget('dataPendapatanRJ');
        Session::forget('dataBPJSRI');
        Session::forget('dataBPJSRJ');
        $userId = Auth::id();
        $data = PercentageJsJp::where('user_id', $userId)->get();
        $data_jp = PercentageJlJtl::where('user_id', $userId)->get();
        $data_dokter = DataDokter::where('user_id', $userId)->get();
        return Inertia::render('Dashboard', [
            'data' => $data,
            'data_jp' => $data_jp,
            'data_dokter' => $data_dokter
        ]);
    }
    public function dataShifting()
    {
        $pathPendapatanRI = Session::get('pathPendapatanRI');
        $pathPendapatanRJ = Session::get('pathPendapatanRJ');
        $pathBPJSRI = Session::get('pathBPJSRI');
        $pathBPJSRJ = Session::get('pathBPJSRJ');
        $response = [];
        if ($pathPendapatanRI && $pathBPJSRI) {
            $dataPendapatanRI = collect($this->getDataPendapatanRS($pathPendapatanRI, 'RI'));
            $dataBPJSRI = collect($this->getDataBpjs($pathBPJSRI, 'RI'));
            $array_BPJS_RI = collect($dataPendapatanRI->whereIn('RM', $dataBPJSRI->pluck('No_RM'))->values()->all());
            
            $array_PiutangBPJS_RI = collect($dataPendapatanRI->where('PENJAMIN','BPJS')->whereNotIn('RM', $dataBPJSRI->pluck('No_RM'))->values()->all());

            $array_grouped_RI = $array_BPJS_RI->groupBy('PASIEN')->map(function ($items) use ($dataBPJSRI, $dataPendapatanRI) {
                $rm = $items[0]['RM'];
                $pasien = $items[0]['PASIEN'];

                $tarifBPJS = $dataBPJSRI->where('No_RM', $rm)->pluck('Total Tarif')->first();
                $inacbg = $dataBPJSRI->where('No_RM', $rm)->pluck('INACBG')->first();
                $tarifRS = array_sum($dataPendapatanRI->where('RM', $rm)->pluck('JUMLAH')->all());

                $dataKonversi = $items->map(function ($item) use ($tarifBPJS, $tarifRS) {
                    $persentase = (intval($item['JUMLAH']) / $tarifRS) * 100;
                    $jumlahKonversi = ($persentase / 100) * $tarifBPJS;
                    return [
                        'JUMLAH' => intval($item['JUMLAH']),
                        'Persentase' => number_format($persentase, 2) . '%',
                        'Jumlah_Konversi' => $jumlahKonversi
                    ];
                })->values()->all();

                return [
                    'RM' => $rm,
                    'PASIEN' => $pasien,
                    'INACBG' => $inacbg,
                    'data' => $items->values()->all(),
                    'Total Tarif' => $tarifBPJS,
                    'Tarif RS' => $tarifRS,
                    'data_konversi' => $dataKonversi
                ];
            })->values()->all();

            $dataPendapatanRI = collect($this->getDataPendapatanRS($pathPendapatanRI, 'RI'));
            $dataGrouped = $dataPendapatanRI->groupBy(function ($item) {
                return trim($item['KLS TARIF']);
            })->map(function ($items) {
                $kelasTarif = trim($items[0]['KLS TARIF']);
                // $jenisJasa = JenisJasaAkun::where('user_id', Auth::id())->where('kelas_tarif', "'$kelasTarif'")->get();
                $jenisJasa = JenisJasaAkun::where('user_id', Auth::id())
                    ->where('kelas_tarif', $kelasTarif)
                    ->pluck('jenis_jasa')
                    ->first();
                return [
                    'data' => $items->values()->all(),
                    'jenis_jasa' => $jenisJasa
                ];
            })->toArray();
            $dataFiltered_JP = array_filter($dataGrouped, function ($item) {
                return $item['jenis_jasa'] === "JP";
            });
            
            $dataWithJumlahData_JP = array_map(function ($item) {
                $jumlahData = count($item['data']);
                $item['jumlah_data'] = $jumlahData;
                return $item;
            }, $dataFiltered_JP);
            
            $totalJumlahData_JP = array_reduce($dataWithJumlahData_JP, function ($carry, $item) {
                return $carry + $item['jumlah_data'];
            }, 0);

            $dataFiltered_JS = array_filter($dataGrouped, function ($item) {
                return $item['jenis_jasa'] === "JS";
            });
            
            $dataWithJumlahData_JS = array_map(function ($item) {
                $jumlahData = count($item['data']);
                $item['jumlah_data'] = $jumlahData;
                return $item;
            }, $dataFiltered_JS);
            
            $totalJumlahData_JS = array_reduce($dataWithJumlahData_JS, function ($carry, $item) {
                return $carry + $item['jumlah_data'];
            }, 0);

            $response = $response + [
                'pasienBPJS_RI' => $array_grouped_RI,
                'totalJPRI' => $totalJumlahData_JP,
                'totalJSRI' => $totalJumlahData_JS,
                'piutangBPJS_RI' => $array_PiutangBPJS_RI
            ];
        }
        if ($pathPendapatanRJ && $pathBPJSRJ) {
            $dataPendapatanRJ = collect($this->getDataPendapatanRS($pathPendapatanRJ, 'RJ'));
            $dataBPJSRJ = collect($this->getDataBpjs($pathBPJSRJ, 'RJ'));
            $array_BPJS_RJ = collect($dataPendapatanRJ->whereIn('RM', $dataBPJSRJ->pluck('No_RM'))->values()->all());
            $array_PiutangBPJS_RJ = collect($dataPendapatanRI->where('PENJAMIN','BPJS')->whereNotIn('RM', $dataBPJSRI->pluck('No_RM'))->values()->all());
            $array_grouped_RJ = $array_BPJS_RJ->groupBy('PASIEN')->map(function ($items) use ($dataBPJSRJ, $dataPendapatanRJ) {
                $rm = $items[0]['RM'];
                $pasien = $items[0]['PASIEN'];

                $tarifBPJS = $dataBPJSRJ->where('No_RM', $rm)->pluck('Total Tarif')->first();
                $inacbg = $dataBPJSRJ->where('No_RM', $rm)->pluck('INACBG')->first();
                $tarifRS = array_sum($dataPendapatanRJ->where('RM', $rm)->pluck('JUMLAH')->all());

                $dataKonversi = $items->map(function ($item) use ($tarifBPJS, $tarifRS) {
                    $persentase = (intval($item['JUMLAH']) / $tarifRS) * 100;
                    $jumlahKonversi = ($persentase / 100) * $tarifBPJS;

                    return [
                        'JUMLAH' => intval($item['JUMLAH']),
                        'Persentase' => number_format($persentase, 2) . '%',
                        'Jumlah_Konversi' => $jumlahKonversi
                    ];
                })->values()->all();

                return [
                    'RM' => $rm,
                    'PASIEN' => $pasien,
                    'INACBG' => $inacbg,
                    'data' => $items->values()->all(),
                    'Total Tarif' => $tarifBPJS,
                    'Tarif RS' => $tarifRS,
                    'data_konversi' => $dataKonversi
                ];
            })->values()->all();
            $dataPendapatanRJ = collect($this->getDataPendapatanRS($pathPendapatanRJ, 'RJ'));
            $dataGrouped = $dataPendapatanRJ->groupBy(function ($item) {
                return trim($item['KLS TARIF']);
            })->map(function ($items) {
                $kelasTarif = trim($items[0]['KLS TARIF']);
                // $jenisJasa = JenisJasaAkun::where('user_id', Auth::id())->where('kelas_tarif', "'$kelasTarif'")->get();
                $jenisJasa = JenisJasaAkun::where('user_id', Auth::id())
                    ->where('kelas_tarif', $kelasTarif)
                    ->pluck('jenis_jasa')
                    ->first();
                return [
                    'data' => $items->values()->all(),
                    'jenis_jasa' => $jenisJasa
                ];
            })->toArray();
            $dataFiltered_JP = array_filter($dataGrouped, function ($item) {
                return $item['jenis_jasa'] === "JP";
            });
            
            $dataWithJumlahData_JP = array_map(function ($item) {
                $jumlahData = count($item['data']);
                $item['jumlah_data'] = $jumlahData;
                return $item;
            }, $dataFiltered_JP);
            
            $totalJumlahData_JP = array_reduce($dataWithJumlahData_JP, function ($carry, $item) {
                return $carry + $item['jumlah_data'];
            }, 0);
            
            $dataFiltered_JS = array_filter($dataGrouped, function ($item) {
                return $item['jenis_jasa'] === "JS";
            });
            
            $dataWithJumlahData_JS = array_map(function ($item) {
                $jumlahData = count($item['data']);
                $item['jumlah_data'] = $jumlahData;
                return $item;
            }, $dataFiltered_JS);
            
            $totalJumlahData_JS = array_reduce($dataWithJumlahData_JS, function ($carry, $item) {
                return $carry + $item['jumlah_data'];
            }, 0);

            $response = $response + [
                'pasienBPJS_RJ' => $array_grouped_RJ,
                'totalJPRJ' => $totalJumlahData_JP,
                'totalJSRJ' => $totalJumlahData_JS,
                'piutangBPJS_RJ' => $array_PiutangBPJS_RJ
            ];
        } else {
            return redirect()->back();
        }
        return Inertia::render('DataShifting', [
            'data' => $response
        ]);
    }
    public function uploadShifting(Request $request)
    {
        ini_set('memory_limit', '1024M');

        if ($request->filePendapatanRI && $request->fileBPJSRI) {
            $request->validate([
                'filePendapatanRI' => 'required|file|mimes:xlsx,xls',
                'fileBPJSRI' => 'required|file|mimes:xlsx,xls'
            ]);
        }

        if ($request->filePendapatanRJ && $request->fileBPJSRJ) {
            $request->validate([
                'filePendapatanRJ' => 'required|file|mimes:xlsx,xls',
                'fileBPJSRJ' => 'required|file|mimes:xlsx,xls'
            ]);
        }

        $file = [
            'pathPendapatanRI' => "",
            'pathPendapatanRJ' => "",
            'pathBPJSRI' => "",
            'pathBPJSRJ' => ""
        ];

        if ($request->file('filePendapatanRI') &&  $request->file('fileBPJSRI')) {
            $file = [
                'pathPendapatanRI' => Session::get('pathPendapatanRI'),
                'pathBPJSRI' => Session::get('pathBPJSRI')
            ];
            $filePendapatanRI = $request->file('filePendapatanRI');
            $fileBPJSRI = $request->file('fileBPJSRI');
            $pathPendapatanRI = $filePendapatanRI->store('uploads');
            $pathBPJSRI = $fileBPJSRI->store('uploads');
            Session::put('pathPendapatanRI', $pathPendapatanRI);
            Session::put('pathBPJSRI', $pathBPJSRI);
        }
        if ($request->file('filePendapatanRJ') &&  $request->file('fileBPJSRJ')) {
            $file = [
                'pathPendapatanRJ' => Session::get('pathPendapatanRJ'),
                'pathBPJSRJ' => Session::get('pathBPJSRJ')
            ];
            $filePendapatanRJ = $request->file('filePendapatanRJ');
            $fileBPJSRJ = $request->file('fileBPJSRJ');
            $pathPendapatanRJ = $filePendapatanRJ->store('uploads');
            $pathBPJSRJ = $fileBPJSRJ->store('uploads');
            Session::put('pathPendapatanRJ', $pathPendapatanRJ);
            Session::put('pathBPJSRJ', $pathBPJSRJ);
        }

        // Storage::delete([$pathPendapatanRI, $pathPendapatanRJ, $pathBPJSRI, $pathBPJSRJ]);

        $response = [
            'message' => 'Data berhasil diupload',
            'file' => $file
        ];
        return response()->json($response);
    }

    public function piutangTakTertagih()
    {

        Session::forget('dataPendapatanRI');
        Session::forget('dataPendapatanRJ');
        Session::forget('dataBPJSRI');
        Session::forget('dataBPJSRJ');

        $pathPendapatanRI = Session::get('pathPendapatanRI');
        $pathPendapatanRJ = Session::get('pathPendapatanRJ');
        $pathBPJSRI = Session::get('pathBPJSRI');
        $pathBPJSRJ = Session::get('pathBPJSRJ');
        $response = [];
        if ($pathPendapatanRI && $pathBPJSRI) {
            $dataPendapatanRI = collect($this->getDataPendapatanRS($pathPendapatanRI, 'RI'));
            $dataBPJSRI = collect($this->getDataBpjs($pathBPJSRI, 'RI'));
            $array_nonBPJS_RI = collect($dataPendapatanRI->whereNotIn('RM', $dataBPJSRI->pluck('No_RM'))->values()->all());
            $array_grouped_RI = $array_nonBPJS_RI->groupBy('PASIEN')->map(function ($items) {
                return [
                    'PASIEN' => $items[0]['PASIEN'],
                    'data' => $items->values()->all()
                ];
            })->values()->all();
            $response = $response + [
                'dataPiutang_RI' => $array_grouped_RI
            ];
        }
        if ($pathPendapatanRJ && $pathBPJSRJ) {
            $dataPendapatanRJ = collect($this->getDataPendapatanRS($pathPendapatanRJ, 'RJ'));
            $dataBPJSRJ = collect($this->getDataBpjs($pathBPJSRJ, 'RJ'));

            $array_nonBPJS_RJ = collect($dataPendapatanRJ->whereNotIn('RM', $dataBPJSRJ->pluck('No_RM'))->all());

            $array_grouped_RJ = $array_nonBPJS_RJ->groupBy('PASIEN')->map(function ($items) {
                return [
                    'PASIEN' => $items[0]['PASIEN'],
                    'data' => $items->values()->all()
                ];
            })->values()->all();
            $response = $response + [
                'dataPiutang_RJ' => $array_grouped_RJ
            ];
        }

        if ($response) {
            return Inertia::render('PiutangBPJS', [
                'dataPiutang' => $response
            ]);
        } else {
            return redirect()->back();
        }
    }
    // private function readDataFromFile($filePath, $headers)
    // {
    //     $rows = Excel::toArray([], $filePath);
    //     $data = [];
    //     $headerRow = $rows[0][0]; // Ambil baris pertama sebagai header
    //     foreach ($rows[0] as $index => $row) {
    //         if ($index === 0) {
    //             continue; // Skip the header row
    //         }
    //         // Validasi jika baris kosong, lewati
    //         if (empty(array_filter($row))) {
    //             continue;
    //         }
    //         $rowData = [];
    //         foreach ($row as $key => $value) {
    //             if (isset($headers[$key])) {
    //                 $header = $headerRow[$key];
    //                 $rowData[$headers[$key]] = $value;
    //             }
    //         }
    //         $data[] = $rowData;
    //     }

    //     return $data;
    // }
    private  function getDataBpjs($path)
    {
        $headers = ['No', 'Tgl_Masuk', 'Tgl_Pulang', 'No_RM', 'Nama_Pasien', 'No_Klaim', 'INACBG', 'Top Up', 'Total Tarif', 'Tarif RS', 'Jenis'];
        $rows = Excel::toArray([], $path);
        $data = [];
        $headerRow = $rows[0][0]; // Ambil baris pertama sebagai header
        foreach ($rows[0] as $index => $row) {
            if ($index === 0) {
                continue; // Skip the header row
            }
            // Validasi jika baris kosong, lewati
            if (empty(array_filter($row))) {
                continue;
            }
            $rowData = [];
            foreach ($row as $key => $value) {
                if (isset($headers[$key])) {
                    $header = $headerRow[$key];
                    $rowData[$headers[$key]] = $value;
                }
            }
            $data[] = $rowData;
        }

        return $data;
    }
    private  function getDataPendapatanRS($path, $type)
    {

        $headers = ['RM', 'NOTRANS', 'TANGGAL', 'PASIEN', 'UNIT', 'FAKTUR', 'PRODUK', 'KLS TARIF', 'OBAT', 'QTY', 'TARIP', 'JUMLAH', 'DOKTER', 'PENJAMIN', 'INVOICE', 'BAYAR'];
        $rows = Excel::toArray([], $path);
        $data = [];
        $headerRow = $rows[0][0]; // Ambil baris pertama sebagai header
        foreach ($rows[0] as $index => $row) {
            if ($index === 0) {
                continue; // Skip the header row
            }
            // Validasi jika baris kosong, lewati
            if (empty(array_filter($row))) {
                continue;
            }
            $rowData = [];
            foreach ($row as $key => $value) {
                if (isset($headers[$key])) {
                    $header = $headerRow[$key];
                    $rowData[$headers[$key]] = $value;
                }
            }
            $data[] = $rowData;
        }

        return $data;
    }
}
