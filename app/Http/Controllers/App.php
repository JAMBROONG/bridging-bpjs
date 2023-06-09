<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\DataDokter;
use App\Models\JenisJasaAkun;
use App\Models\KategoriPendapatan;
use App\Models\PercentageJlJtl;
use App\Models\PercentageJsJp;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class App extends Controller
{
    public function index()
    {
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
    public function clear()
    {


        $pathPendapatanRI = Session::get('pathPendapatanRI');
        $pathPendapatanRJ = Session::get('pathPendapatanRJ');
        $pathBPJSRI = Session::get('pathBPJSRI');
        $pathBPJSRJ = Session::get('pathBPJSRJ');

        // Hapus file pathPendapatanRI jika ada
        if ($pathPendapatanRI && File::exists($pathPendapatanRI)) {
            File::delete($pathPendapatanRI);
        }

        // Hapus file pathPendapatanRJ jika ada
        if ($pathPendapatanRJ && File::exists($pathPendapatanRJ)) {
            File::delete($pathPendapatanRJ);
        }

        // Hapus file pathBPJSRI jika ada
        if ($pathBPJSRI && File::exists($pathBPJSRI)) {
            File::delete($pathBPJSRI);
        }

        // Hapus file pathBPJSRJ jika ada
        if ($pathBPJSRJ && File::exists($pathBPJSRJ)) {
            File::delete($pathBPJSRJ);
        }

        // Hapus session setelah file dihapus
        Session::forget('pathPendapatanRI');
        Session::forget('pathPendapatanRJ');
        Session::forget('pathBPJSRI');
        Session::forget('pathBPJSRJ');
        Session::forget('dataPendapatanRI');
        Session::forget('dataPendapatanRJ');
        Session::forget('dataBPJSRI');
        Session::forget('dataBPJSRJ');
        Session::forget('jasaPelayananRI');
        Session::forget('jasaPelayananRJ');
        Session::forget('jasaSaranaRI');
        Session::forget('jasaSaranaRJ');

        return redirect()->back();
    }
    public function getPasienBPJS($pathPendapatanRI, $pathBPJSRI)
    {
        $dataPendapatanRI = collect($this->getDataPendapatanRS($pathPendapatanRI, 'RI'));
        $dataBPJSRI = collect($this->getDataBpjs($pathBPJSRI, 'RI'));
        return collect($dataPendapatanRI->whereIn('RM', $dataBPJSRI->pluck('No_RM'))->values()->all());
    }
    public function getPasienNonBPJS($pathPendapatanRI, $pathBPJSRI)
    {
        $dataPendapatanRI = collect($this->getDataPendapatanRS($pathPendapatanRI, 'RI'));
        $dataBPJSRI = collect($this->getDataBpjs($pathBPJSRI, 'RI'));
        return collect($dataPendapatanRI->whereNotIn('RM', $dataBPJSRI->pluck('No_RM'))->where('PENJAMIN', '!=', 'BPJS')->values()->all());
    }
    public function getPiutangBPJS($pathPendapatanRI, $pathBPJSRI)
    {
        $dataPendapatanRI = collect($this->getDataPendapatanRS($pathPendapatanRI, 'RI'));
        $dataBPJSRI = collect($this->getDataBpjs($pathBPJSRI, 'RI'));
        return collect($dataPendapatanRI->where('PENJAMIN', 'BPJS')->whereNotIn('RM', $dataBPJSRI->pluck('No_RM'))->values()->all());
    }
    public function getDataJPNonBPJS($pasienNonBPJS)
    {
        $dataJPRI_NonBPJS = $pasienNonBPJS->groupBy('KLS TARIF')->map(function ($items, $kelasTarif) use ($pasienNonBPJS) {
            $jenisJasa = JenisJasaAkun::where('user_id', Auth::id())
                ->where('kelas_tarif', $kelasTarif)
                ->pluck('jenis_jasa')
                ->first();
            if ($jenisJasa === "JP") {
                return [
                    'KLS TARIF' => $kelasTarif,
                    'JENIS JASA' => $jenisJasa,
                    'DATA' => $items->groupBy('PASIEN')->map(function ($item) use ($pasienNonBPJS, $kelasTarif) {

                        $rm = $item[0]['RM'];
                        $pasien = $item[0]['PASIEN'];
                        $dataPasien = $pasienNonBPJS->where('RM', $rm)->where('KLS TARIF', $kelasTarif)->values()->all();
                        return [
                            'PASIEN' => $pasien,
                            'RM' => $rm,
                            'data' => $dataPasien,
                        ];
                    })->values()->all()
                ];
            }
        })->filter(function ($item) {
            return isset($item);
        })->values()->all();

        return [
            'dataJP_NonBPJS' => $dataJPRI_NonBPJS,
            'total_data' => count($dataJPRI_NonBPJS)
        ];
    }
    public function getDataJPBPJS($pasienBPJS, $dataBPJSRI)
    {
        $dataJPRI_BPJS = $pasienBPJS->groupBy('KLS TARIF')->map(function ($items, $kelasTarif) use ($dataBPJSRI, $pasienBPJS) {
            $jenisJasa = JenisJasaAkun::where('user_id', Auth::id())
                ->where('kelas_tarif', $kelasTarif)
                ->pluck('jenis_jasa')
                ->first();
            if ($jenisJasa === "JP") {
                $cur_rm = 0;
                return [
                    'KLS TARIF' => $kelasTarif,
                    'JENIS JASA' => $jenisJasa,
                    'DATA' => $items->groupBy('PASIEN')->map(function ($item) use ($dataBPJSRI, $pasienBPJS, $kelasTarif, $cur_rm) {

                        $rm = $item[0]['RM'];

                        $pasien = $item[0]['PASIEN'];
                        $tarifBPJS = $dataBPJSRI->where('No_RM', $rm)->pluck('Total Tarif')->first();
                        $inacbg = $dataBPJSRI->where('No_RM', $rm)->pluck('INACBG')->first();
                        $tarifRS = array_sum($pasienBPJS->where('RM', $rm)->pluck('JUMLAH')->all());

                        $dataPasien = $pasienBPJS->where('RM', $rm)->where('KLS TARIF', $kelasTarif)->values()->all();

                        $dataKonversi = collect($dataPasien)->map(function ($item) use ($tarifBPJS, $tarifRS) {
                            $persentase = (intval($item['JUMLAH']) / $tarifRS) * 100;
                            $jumlahKonversi = ($persentase / 100) * $tarifBPJS;

                            return [
                                'JUMLAH' => intval($item['JUMLAH']),
                                'Persentase' => number_format($persentase, 2) . '%',
                                'Jumlah_Konversi' => $jumlahKonversi
                            ];
                        })->values()->all();

                        return [
                            'PASIEN' => $pasien,
                            'RM' => $rm,
                            'INACBG' => $inacbg,
                            'data' => $dataPasien,
                            'Tarif RS' => $tarifRS,
                            'Total Tarif' => $tarifBPJS,
                            'Data Konversi' => $dataKonversi,
                            'Jumlah Data' => count($dataPasien) // Hitung jumlah data dalam array 'data'
                        ];
                    })->values()->all()
                ];
            }
        })->filter(function ($item) {
            return isset($item);
        })->values()->all();

        return [
            'dataJP_BPJS' => $dataJPRI_BPJS,
            'total_data' => count($dataJPRI_BPJS)
        ];
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
            $array_BPJS_RI = $this->getPasienBPJS($pathPendapatanRI, $pathBPJSRI);

            $array_PiutangBPJS_RI = $this->getPiutangBPJS($pathPendapatanRI, $pathBPJSRI);

            $array_grouped_RI = $array_BPJS_RI->groupBy('PASIEN')->map(function ($items) use ($dataBPJSRI, $array_BPJS_RI) {
                $rm = $items[0]['RM'];
                $pasien = $items[0]['PASIEN'];

                $tarifBPJS = $dataBPJSRI->where('No_RM', $rm)->pluck('Total Tarif')->first();
                $inacbg = $dataBPJSRI->where('No_RM', $rm)->pluck('INACBG')->first();
                $tarifRS = array_sum($array_BPJS_RI->where('RM', $rm)->pluck('JUMLAH')->all());

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

            Session::put('jasaPelayananRI', $dataFiltered_JP);
            Session::put('jasaSaranaRI', $dataFiltered_JS);

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
            $array_PiutangBPJS_RJ = collect($dataPendapatanRI->where('PENJAMIN', 'BPJS')->whereNotIn('RM', $dataBPJSRI->pluck('No_RM'))->values()->all());
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

            Session::put('jasaPelayananRJ', $dataFiltered_JP);
            Session::put('jasaSaranaRJ', $dataFiltered_JS);

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
    public function shiftingAll()
    {
        $pathPendapatanRI = Session::get('pathPendapatanRI');
        $pathPendapatanRJ = Session::get('pathPendapatanRJ');
        $pathBPJSRI = Session::get('pathBPJSRI');
        $pathBPJSRJ = Session::get('pathBPJSRJ');
        $response = [];
        if ($pathPendapatanRI && $pathBPJSRI) {

            // perhitungan pasien non bpjs
            $pasienNonBPJS = $this->getPasienNonBPJS($pathPendapatanRI, $pathBPJSRI);

            $breakdownRINonBPJS = $pasienNonBPJS->groupBy('KLS TARIF')->map(function ($items, $kelasTarif) use ($pasienNonBPJS) {
                $dataPasien = collect($pasienNonBPJS->where('KLS TARIF', trim($kelasTarif))->values()->all());
                $jumlah = $dataPasien->reduce(function ($carry, $item) {
                    return $carry + intval($item['JUMLAH']);
                }, 0);
                $dataKategoriPendapatanId = JenisJasaAkun::where('kelas_tarif', trim($kelasTarif))->first()['kategori_pendapatans_id'];
                return [
                    'KLS TARIF' => $kelasTarif,
                    'jumlah' => $jumlah,
                    'kategori_pendapatanId' => $dataKategoriPendapatanId
                ];
            })->filter(function ($item) {
                return isset($item);
            })->values()->all();
            $kategoriPendapatan = collect(KategoriPendapatan::all());
            $groupedData = collect($breakdownRINonBPJS)->groupBy(function ($item) use ($kategoriPendapatan) {
                $kategoriId = $item['kategori_pendapatanId'];
                $kategori = $kategoriPendapatan->firstWhere('id', $kategoriId);

                return $kategori ? $kategori->kategori : null;
            })->map(function ($items, $kategori) {
                $jumlah = collect($items)->reduce(function ($carry, $item) {
                    return $carry + $item['jumlah'];
                }, 0);

                return [
                    'kategori' => $kategori,
                    'jumlah' => $jumlah,
                ];
            })->values()->all();
            $laporanNonBPJS = $groupedData;
            $totalJumlahKonversiNonBPJS = array_reduce($laporanNonBPJS, function ($carry, $items) {
                return $carry + $items['jumlah'];
            }, 0);


            // akhir dari perhitungan pasien non bpjs


            //perhitunga pasien bpjs
            $dataBPJSRI = collect($this->getDataBpjs($pathBPJSRI, 'RI'));
            $pasienBPJS = $this->getPasienBPJS($pathPendapatanRI, $pathBPJSRI);
            $array_grouped_RI = $pasienBPJS->groupBy('PASIEN')->map(function ($items) use ($dataBPJSRI, $pasienBPJS) {
                $rm = $items[0]['RM'];

                $tarifBPJS = $dataBPJSRI->where('No_RM', $rm)->pluck('Total Tarif')->first();
                $tarifRS = array_sum($pasienBPJS->where('RM', $rm)->pluck('JUMLAH')->all());

                $dataKonversi = $items->map(function ($item) use ($tarifBPJS, $tarifRS) {
                    $persentase = (intval($item['JUMLAH']) / $tarifRS) * 100;
                    $jumlahKonversi = ($persentase / 100) * $tarifBPJS;
                    return [
                        'Kelas Tarif' => $item['KLS TARIF'],
                        'Jumlah_Konversi' => $jumlahKonversi
                    ];
                })->values()->all();
                return  $dataKonversi;
            })->values()->all();

            $array_grouped_by_kelas_tarif = collect($array_grouped_RI)->groupBy(function ($item) {
                return $item[0]['Kelas Tarif'];
            })->map(function ($items) {
                $jumlah = $items->sum(function ($item) {
                    return $item[0]['Jumlah_Konversi'];
                });

                return [
                    'kelas tarif' => $items[0][0]['Kelas Tarif'],
                    'jumlah' => $jumlah
                ];
            })->values()->all();
            $breakdownRIBPJS = collect($array_grouped_by_kelas_tarif)->groupBy('kelas tarif')->map(function ($items, $kelasTarif) use ($pasienBPJS) {
                $dataKategoriPendapatanId = JenisJasaAkun::where('kelas_tarif', trim($kelasTarif))->first()['kategori_pendapatans_id'];
                return [
                    'KLS TARIF' => trim($kelasTarif),
                    'jumlah' => $items[0]['jumlah'],
                    'kategori_pendapatanId' => $dataKategoriPendapatanId
                ];
            })->filter(function ($item) {
                return isset($item);
            })->values()->all();
            $kategoriPendapatan = collect(KategoriPendapatan::all());
            $groupedData = collect($breakdownRIBPJS)->groupBy(function ($item) use ($kategoriPendapatan) {
                $kategoriId = $item['kategori_pendapatanId'];
                $kategori = $kategoriPendapatan->firstWhere('id', $kategoriId);

                return $kategori ? $kategori->kategori : null;
            })->map(function ($items, $kategori) {
                $jumlah = collect($items)->reduce(function ($carry, $item) {
                    return $carry + $item['jumlah'];
                }, 0);

                return [
                    'kategori' => $kategori,
                    'jumlah' => $jumlah,
                ];
            })->values()->all();
            $laporanBPJS = $groupedData;

            $totalJumlahKonversiBPJS = array_reduce($laporanBPJS, function ($carry, $items) {
                return $carry + $items['jumlah'];
            }, 0);
            // akhir dari perhitungan pasien bpjs

            $laporanRI = array_merge_recursive($laporanNonBPJS, $laporanBPJS);

            $laporanRI = collect($laporanRI)->groupBy('kategori')->map(function ($items) {
                $jumlah = intval($items->sum('jumlah'));
                return [
                    'kategori' => $items[0]['kategori'],
                    'jumlah' => intval($jumlah)
                ];
            })->values()->all();


            $response = $response + [
                'pendapatanRINonBPJS' => intval($totalJumlahKonversiNonBPJS),
                'pendapatanRIBPJS' => intval($totalJumlahKonversiBPJS),
                'laporanRI' => $laporanRI
            ];
        }
        if ($pathPendapatanRJ && $pathBPJSRJ) {

            // perhitungan pasien non bpjs
            $pasienNonBPJS = $this->getPasienNonBPJS($pathPendapatanRJ, $pathBPJSRJ);
            $breakdownRJNonBPJS = $pasienNonBPJS->groupBy('KLS TARIF')->map(function ($items, $kelasTarif) use ($pasienNonBPJS) {
                $dataPasien = collect($pasienNonBPJS->where('KLS TARIF', trim($kelasTarif))->values()->all());
                $jumlah = $dataPasien->reduce(function ($carry, $item) {
                    return $carry + intval($item['JUMLAH']);
                }, 0);
                $dataKategoriPendapatanId = JenisJasaAkun::where('kelas_tarif', trim($kelasTarif))->first()['kategori_pendapatans_id'];
                return [
                    'KLS TARIF' => $kelasTarif,
                    'jumlah' => $jumlah,
                    'kategori_pendapatanId' => $dataKategoriPendapatanId
                ];
            })->filter(function ($item) {
                return isset($item);
            })->values()->all();
            $kategoriPendapatan = collect(KategoriPendapatan::all());
            $groupedData = collect($breakdownRJNonBPJS)->groupBy(function ($item) use ($kategoriPendapatan) {
                $kategoriId = $item['kategori_pendapatanId'];
                $kategori = $kategoriPendapatan->firstWhere('id', $kategoriId);

                return $kategori ? $kategori->kategori : null;
            })->map(function ($items, $kategori) {
                $jumlah = collect($items)->reduce(function ($carry, $item) {
                    return $carry + $item['jumlah'];
                }, 0);

                return [
                    'kategori' => $kategori,
                    'jumlah' => $jumlah,
                ];
            })->values()->all();
            $laporanNonBPJS = $groupedData;
            $totalJumlahKonversiNonBPJS = array_reduce($laporanNonBPJS, function ($carry, $items) {
                return $carry + $items['jumlah'];
            }, 0);
            // akhir dari perhitungan pasien non bpjs


            //perhitunga pasien bpjs
            $dataBPJSRJ = collect($this->getDataBpjs($pathBPJSRJ, 'RJ'));
            $pasienBPJS = $this->getPasienBPJS($pathPendapatanRJ, $pathBPJSRJ);
            $array_grouped_RJ = $pasienBPJS->groupBy('PASIEN')->map(function ($items) use ($dataBPJSRJ, $pasienBPJS) {
                $rm = $items[0]['RM'];
                $tarifBPJS = $dataBPJSRJ->where('No_RM', $rm)->pluck('Total Tarif')->first();
                $tarifRS = array_sum($pasienBPJS->where('RM', $rm)->pluck('JUMLAH')->all());

                $dataKonversi = $items->map(function ($item) use ($tarifBPJS, $tarifRS) {
                    $persentase = (intval($item['JUMLAH']) / $tarifRS) * 100;
                    $jumlahKonversi = ($persentase / 100) * $tarifBPJS;
                    return [
                        'Kelas Tarif' => $item['KLS TARIF'],
                        'Jumlah_Konversi' => $jumlahKonversi
                    ];
                })->values()->all();
                return  $dataKonversi;
            })->values()->all();

            $array_grouped_by_kelas_tarif = collect($array_grouped_RJ)->groupBy(function ($item) {
                return $item[0]['Kelas Tarif'];
            })->map(function ($items) {
                $jumlah = $items->sum(function ($item) {
                    return $item[0]['Jumlah_Konversi'];
                });

                return [
                    'kelas tarif' => $items[0][0]['Kelas Tarif'],
                    'jumlah' => $jumlah
                ];
            })->values()->all();

            $breakdownRJBPJS = collect($array_grouped_by_kelas_tarif)->groupBy('kelas tarif')->map(function ($items, $kelasTarif) use ($pasienBPJS) {
                $dataKategoriPendapatanId = JenisJasaAkun::where('kelas_tarif', trim($kelasTarif))->first()['kategori_pendapatans_id'];
                return [
                    'KLS TARIF' => trim($kelasTarif),
                    'jumlah' => $items[0]['jumlah'],
                    'kategori_pendapatanId' => $dataKategoriPendapatanId
                ];
            })->filter(function ($item) {
                return isset($item);
            })->values()->all();
            $kategoriPendapatan = collect(KategoriPendapatan::all());
            $groupedData = collect($breakdownRJBPJS)->groupBy(function ($item) use ($kategoriPendapatan) {
                $kategoriId = $item['kategori_pendapatanId'];
                $kategori = $kategoriPendapatan->firstWhere('id', $kategoriId);

                return $kategori ? $kategori->kategori : null;
            })->map(function ($items, $kategori) {
                $jumlah = collect($items)->reduce(function ($carry, $item) {
                    return $carry + $item['jumlah'];
                }, 0);

                return [
                    'kategori' => $kategori,
                    'jumlah' => $jumlah,
                ];
            })->values()->all();
            $laporanBPJS = $groupedData;

            $totalJumlahKonversiBPJS = array_reduce($laporanBPJS, function ($carry, $items) {
                return $carry + $items['jumlah'];
            }, 0);


            $laporanRJ = array_merge_recursive($laporanNonBPJS, $laporanBPJS);

            $laporanRJ = collect($laporanRJ)->groupBy('kategori')->map(function ($items) {
                $jumlah = $items->sum('jumlah');
                return [
                    'kategori' => $items[0]['kategori'],
                    'jumlah' => intval($jumlah)
                ];
            })->values()->all();

            $response = $response + [
                'pendapatanRJNonBPJS' => intval($totalJumlahKonversiNonBPJS),
                'pendapatanRJBPJS' => intval($totalJumlahKonversiBPJS),
                'laporanRJ' => $laporanRJ
            ];
        }


        return Inertia::render('ShiftingReportAll', [
            'data' => $response
        ]);
    }
    public function shiftingJS()
    {
        $pathPendapatanRI = Session::get('pathPendapatanRI');
        $pathPendapatanRJ = Session::get('pathPendapatanRJ');
        $pathBPJSRI = Session::get('pathBPJSRI');
        $pathBPJSRJ = Session::get('pathBPJSRJ');
        $response = [];
        if ($pathPendapatanRI && $pathBPJSRI) {
        }
        if ($pathPendapatanRJ && $pathBPJSRJ) {
        }
        die;
    }

    public function shiftingJP()
    {
        $pathPendapatanRI = Session::get('pathPendapatanRI');
        $pathPendapatanRJ = Session::get('pathPendapatanRJ');
        $pathBPJSRI = Session::get('pathBPJSRI');
        $pathBPJSRJ = Session::get('pathBPJSRJ');
        $response = [];

        if ($pathPendapatanRI && $pathBPJSRI) {
            $pasienNonBPJS = $this->getPasienNonBPJS($pathPendapatanRI, $pathBPJSRI);
            $pasienBPJS = $this->getPasienBPJS($pathPendapatanRI, $pathBPJSRI);
            $dataBPJSRI = collect($this->getDataBpjs($pathBPJSRI, 'RI'));

            $dataJPRI_NonBPJS = $this->getDataJPNonBPJS($pasienNonBPJS);
            $dataJPRI_BPJS = $this->getDataJPBPJS($pasienBPJS, $dataBPJSRI);

            $response = $response + [
                'dataJPRI_NonBPJS' => $dataJPRI_NonBPJS['dataJP_NonBPJS'],
                'dataJPRI_BPJS' => $dataJPRI_BPJS['dataJP_BPJS'],
                'total_dataJPRI_BPJS' => $dataJPRI_BPJS['total_data'],
                'total_dataJPRI_NonBPJS' => $dataJPRI_NonBPJS['total_data'],
            ];
        }
        if ($pathPendapatanRJ && $pathBPJSRJ) {
            $pasienNonBPJS = $this->getPasienNonBPJS($pathPendapatanRJ, $pathBPJSRJ);
            $pasienBPJS = $this->getPasienBPJS($pathPendapatanRJ, $pathBPJSRJ);
            $dataBPJSRJ = collect($this->getDataBpjs($pathBPJSRJ, 'RJ'));

            $dataJPRJ_NonBPJS = $this->getDataJPNonBPJS($pasienNonBPJS);
            $dataJPRJ_BPJS = $this->getDataJPBPJS($pasienBPJS, $dataBPJSRJ);

            $response = $response + [
                'dataJPRJ_NonBPJS' => $dataJPRJ_NonBPJS['dataJP_NonBPJS'],
                'dataJPRJ_BPJS' => $dataJPRJ_BPJS['dataJP_BPJS'],
                'total_dataJPRJ_BPJS' => $dataJPRJ_BPJS['total_data'],
                'total_dataJPRJ_NonBPJS' => $dataJPRJ_NonBPJS['total_data'],
            ];
        } else {
            return redirect()->back();
        }
        // dd($response['dataJPRI_BPJS']); die;
        return Inertia::render('ShiftingJasaPelayanan', [
            'data' => $response
        ]);
    }


    // ======================== ALLOCATION AND DISTRIBUTION ======================== //
    public function uploadDistribution(Request $request)
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

        $response = [
            'message' => 'Data berhasil diupload',
            'file' => $file
        ];
        return response()->json($response);
    }
    public function dataDistribution()
    {
        $pathPendapatanRI = Session::get('pathPendapatanRI');
        $pathPendapatanRJ = Session::get('pathPendapatanRJ');
        $pathBPJSRI = Session::get('pathBPJSRI');
        $pathBPJSRJ = Session::get('pathBPJSRJ');
        $response = [];
        if (empty(PercentageJsJp::where('user_id', Auth::id())->first()['jp'])) {
            return redirect()->back();
        }
        if ($pathPendapatanRI && $pathBPJSRI) {
            $dataBPJSRI = collect($this->getDataBpjs($pathBPJSRI, 'RI'));

            $persentaseJP = PercentageJsJp::where('user_id', Auth::id())->first()['jp'];
            $persentaseJS = PercentageJsJp::where('user_id', Auth::id())->first()['js'];
            // PASIEN BPJS
            $array_BPJS_RI = $this->getPasienBPJS($pathPendapatanRI, $pathBPJSRI);
            $kelasTarifMapping = collect(JenisJasaAkun::where('user_id', Auth::id())->get());
            $array_BPJS_RIWithJenisJasa = $array_BPJS_RI->map(function ($item) use ($kelasTarifMapping, $array_BPJS_RI, $persentaseJP, $persentaseJS) {
                $kelasTarif = trim($item['KLS TARIF']);
                $jenisJasa = $kelasTarifMapping->where('kelas_tarif', $kelasTarif)->first()['jenis_jasa'];
                $jumlahNominalPerPasien = collect($array_BPJS_RI)->where('RM', $item['RM'])->all();
                $jumlahNominalPerPasien = collect($jumlahNominalPerPasien)->sum(function ($items) {
                    return intval($items['JUMLAH']);
                });

                // Simpan hasil penggabungan ke dalam variabel $item
                $item = array_merge($item, ['JENIS JASA' => $jenisJasa]);
                $item = array_merge($item, ['JUMLAH NOMINAL' => $jumlahNominalPerPasien]);

                return $item;
            });

            $array_BPJS_RIWithJenisJasa = $array_BPJS_RIWithJenisJasa->groupBy('PASIEN')->map(function ($item) use ($dataBPJSRI, $persentaseJP, $persentaseJS, $array_BPJS_RIWithJenisJasa) {
                $array_BPJS_RIWithJenisJasaJS = collect($array_BPJS_RIWithJenisJasa->where('RM', $item[0]['RM'])->where('JENIS JASA', 'JS')->all());
                $array_BPJS_RIWithJenisJasaJP = collect($array_BPJS_RIWithJenisJasa->where('RM', $item[0]['RM'])->where('JENIS JASA', 'JP')->all());
                $jumlahNominal = $item->sum(function ($items) {
                    return intval($items['JUMLAH']);
                });
                $array = [];
                $nominalJP = (($jumlahNominal * $persentaseJP) / 100);
                $nominalJS = (($jumlahNominal * $persentaseJS) / 100);
                if (count($array_BPJS_RIWithJenisJasaJS) == 0) {
                    $item = $item->map(function ($items) use ($jumlahNominal, $nominalJP, $persentaseJP, $persentaseJS) {
                        $persentase = floatval($items['JUMLAH']) / $jumlahNominal;
                        if ($items['JENIS JASA'] == "JP") {
                            $jumlahKonversi = $persentase * $nominalJP;
                            $items = array_merge($items, ['jumlahSetelahDiKonversi' => $jumlahKonversi]);
                            $items = array_merge($items, ['jumlahHasilPersentase' => $nominalJP]);
                            $items = array_merge($items, ['sisa_nominal' => number_format((intval(intval($items['JUMLAH']) - $jumlahKonversi)), 0, ',', '.') . ' (B. Gaji)']);
                            $items = array_merge($items, ['persentase' => $persentaseJP . '%']);
                        }
                        return $items;
                    });
                }
                if (count($array_BPJS_RIWithJenisJasaJP) == 0) {
                    $item = $item->map(function ($items) use ($jumlahNominal, $nominalJS, $persentaseJS) {
                        $persentase = floatval($items['JUMLAH']) / $jumlahNominal;
                        if ($items['JENIS JASA'] == "JS") {
                            $jumlahKonversi = $persentase * $nominalJS;
                            $items = array_merge($items, ['jumlahSetelahDiKonversi' => $jumlahKonversi]);
                            $items = array_merge($items, ['jumlahHasilPersentase' => $nominalJS]);
                            $items = array_merge($items, ['sisa_nominal' => number_format((intval(intval($items['JUMLAH']) - $jumlahKonversi)), 0, ',', '.') . ' (P. BPJS)']);
                            $items = array_merge($items, ['persentase' => $persentaseJS . '%']);
                        }
                        return $items;
                    });
                } else {
                    $item = $item->map(function ($items) use ($jumlahNominal, $nominalJS, $nominalJP, $persentaseJP, $persentaseJS) {
                        $persentase = floatval($items['JUMLAH']) / $jumlahNominal;
                        if ($items['JENIS JASA'] == "JS") {
                            $jumlahKonversi = $persentase * $nominalJS;
                            $items = array_merge($items, ['jumlahSetelahDiKonversi' => $jumlahKonversi]);
                            $items = array_merge($items, ['jumlahHasilPersentase' => $nominalJS]);
                            $items = array_merge($items, ['sisa_nominal' => number_format((intval(intval($items['JUMLAH']) - $jumlahKonversi)), 0, ',', '.') . ' (B. Gaji)']);
                            $items = array_merge($items, ['persentase' => $persentaseJS . '%']);
                        } else if ($items['JENIS JASA'] == "JP") {
                            $jumlahKonversi = $persentase * $nominalJP;
                            $items = array_merge($items, ['jumlahSetelahDiKonversi' => $jumlahKonversi]);
                            $items = array_merge($items, ['jumlahHasilPersentase' => $nominalJP]);
                            $items = array_merge($items, ['sisa_nominal' => number_format((intval(intval($items['JUMLAH']) - $jumlahKonversi)), 0, ',', '.') . ' (P. BPJS)']);
                            $items = array_merge($items, ['persentase' => $persentaseJP . '%']);
                        }
                        return $items;
                    });
                }
                $bebanGaji = collect($item)->where('JENIS JASA', 'JS')->all();
                $pendapatanBPJS = collect($item)->where('JENIS JASA', 'JP')->all();
                if (count($bebanGaji) <= 0) {
                    $bebanGaji = $jumlahNominal;
                    $pendapatanBPJS = 0;
                } else
                if (count($pendapatanBPJS) <= 0) {
                    $bebanGaji = 0;
                    $pendapatanBPJS = $jumlahNominal;
                } else {
                    $bebanGaji = collect($bebanGaji)->sum(function ($item) {
                        return intval($item['JUMLAH']);
                    });
                    $pendapatanBPJS = collect($pendapatanBPJS)->sum(function ($item) {
                        return intval($item['JUMLAH']);
                    });
                }

                $rm = $item[0]['RM'];
                $pasien = $item[0]['PASIEN'];

                $tarifBPJS = $dataBPJSRI->where('No_RM', $rm)->pluck('Total Tarif')->first();
                $inacbg = $dataBPJSRI->where('No_RM', $rm)->pluck('INACBG')->first();

                $dataKonversi = $item->map(function ($items) use ($tarifBPJS, $jumlahNominal) {
                    $persentase = (intval($items['jumlahSetelahDiKonversi']) / $jumlahNominal) * 100;
                    $jumlahKonversi = ($persentase / 100) * $tarifBPJS;
                    return [
                        'JUMLAH' => intval($items['jumlahSetelahDiKonversi']),
                        'Persentase' => $persentase,
                        'Jumlah_Konversi' => $jumlahKonversi
                    ];
                })->values()->all();

                $array = $array + ['RM' => $rm];
                $array = $array + ['PASIEN' => $pasien];
                $array = $array + ['tarifBPJS' => $tarifBPJS];
                $array = $array + ['INACBG' => $inacbg];
                $array = $array + ['Beban Gaji' => $bebanGaji];
                $array = $array + ['Pendapatan BPJS' => $pendapatanBPJS];
                $array = $array + ['Jumlah Nominal' => $jumlahNominal];
                $array = $array + ['Data Konversi' => $dataKonversi];
                $array = $array + ['data' => $item->values()->all()];
                return  $array;
            });
            $updatedArray = $array_BPJS_RIWithJenisJasa->values()->all();

            Session::put('dataPasienDistributionBPJSRI', $updatedArray);


            $array_PiutangBPJS_RI = $this->getPiutangBPJS($pathPendapatanRI, $pathBPJSRI);
            $totalJumlahData_JP = 0;
            $totalJumlahData_JS = 0;
            $response = $response + [
                'pasienBPJS_RI' => $updatedArray,
                'totalJPRI' => $totalJumlahData_JP,
                'totalJSRI' => $totalJumlahData_JS,
                'piutangBPJS_RI' => $array_PiutangBPJS_RI
            ];
        }
        if ($pathPendapatanRJ && $pathBPJSRJ) {
            $dataBPJSRJ = collect($this->getDataBpjs($pathBPJSRJ, 'RJ'));

            $persentaseJP = PercentageJsJp::where('user_id', Auth::id())->first()['jp'];
            $persentaseJS = PercentageJsJp::where('user_id', Auth::id())->first()['js'];
            // PASIEN BPJS
            $array_BPJS_RJ = $this->getPasienBPJS($pathPendapatanRJ, $pathBPJSRJ);
            $kelasTarifMapping = collect(JenisJasaAkun::where('user_id', Auth::id())->get());
            $array_BPJS_RJWithJenisJasa = $array_BPJS_RJ->map(function ($item) use ($kelasTarifMapping, $array_BPJS_RJ) {
                $kelasTarif = trim($item['KLS TARIF']);
                $jenisJasa = $kelasTarifMapping->where('kelas_tarif', $kelasTarif)->first()['jenis_jasa'];
                $jumlahNominalPerPasien = collect($array_BPJS_RJ)->where('RM', $item['RM'])->all();
                $jumlahNominalPerPasien = collect($jumlahNominalPerPasien)->sum(function ($items) {
                    return intval($items['JUMLAH']);
                });

                // Simpan hasil penggabungan ke dalam variabel $item
                $item = array_merge($item, ['JENIS JASA' => $jenisJasa]);
                $item = array_merge($item, ['JUMLAH NOMINAL' => $jumlahNominalPerPasien]);

                return $item;
            });

            $array_BPJS_RJWithJenisJasa = $array_BPJS_RJWithJenisJasa->groupBy('PASIEN')->map(function ($item) use ($dataBPJSRJ, $persentaseJP, $persentaseJS, $array_BPJS_RJWithJenisJasa) {
                $array_BPJS_RJWithJenisJasaJS = collect($array_BPJS_RJWithJenisJasa->where('RM', $item[0]['RM'])->where('JENIS JASA', 'JS')->all());
                $array_BPJS_RJWithJenisJasaJP = collect($array_BPJS_RJWithJenisJasa->where('RM', $item[0]['RM'])->where('JENIS JASA', 'JP')->all());
                $jumlahNominal = $item->sum(function ($items) {
                    return intval($items['JUMLAH']);
                });
                $array = [];
                $nominalJP = (($jumlahNominal * $persentaseJP) / 100);
                $nominalJS = (($jumlahNominal * $persentaseJS) / 100);
                if (count($array_BPJS_RJWithJenisJasaJS) == 0) {
                    $item = $item->map(function ($items) use ($jumlahNominal, $nominalJP, $persentaseJP, $persentaseJS) {
                        $persentase = floatval($items['JUMLAH']) / $jumlahNominal;
                        if ($items['JENIS JASA'] == "JP") {
                            $jumlahKonversi = $persentase * $nominalJP;
                            $items = array_merge($items, ['jumlahSetelahDiKonversi' => $jumlahKonversi]);
                            $items = array_merge($items, ['jumlahHasilPersentase' => $nominalJP]);
                            $items = array_merge($items, ['sisa_nominal' => number_format((intval(intval($items['JUMLAH']) - $jumlahKonversi)), 0, ',', '.') . ' (B. Gaji)']);
                            $items = array_merge($items, ['persentase' => $persentaseJP . '%']);
                        }
                        return $items;
                    });
                }
                if (count($array_BPJS_RJWithJenisJasaJP) == 0) {
                    $item = $item->map(function ($items) use ($jumlahNominal, $nominalJS, $persentaseJS) {
                        $persentase = floatval($items['JUMLAH']) / $jumlahNominal;
                        if ($items['JENIS JASA'] == "JS") {
                            $jumlahKonversi = $persentase * $nominalJS;
                            $items = array_merge($items, ['jumlahSetelahDiKonversi' => $jumlahKonversi]);
                            $items = array_merge($items, ['jumlahHasilPersentase' => $nominalJS]);
                            $items = array_merge($items, ['sisa_nominal' => number_format((intval(intval($items['JUMLAH']) - $jumlahKonversi)), 0, ',', '.') . ' (P. BPJS)']);
                            $items = array_merge($items, ['persentase' => $persentaseJS . '%']);
                        }
                        return $items;
                    });
                } else {
                    $item = $item->map(function ($items) use ($jumlahNominal, $nominalJS, $nominalJP, $persentaseJP, $persentaseJS) {
                        $persentase = floatval($items['JUMLAH']) / $jumlahNominal;
                        if ($items['JENIS JASA'] == "JS") {
                            $jumlahKonversi = $persentase * $nominalJS;
                            $items = array_merge($items, ['jumlahSetelahDiKonversi' => $jumlahKonversi]);
                            $items = array_merge($items, ['jumlahHasilPersentase' => $nominalJS]);
                            $items = array_merge($items, ['sisa_nominal' => number_format((intval(intval($items['JUMLAH']) - $jumlahKonversi)), 0, ',', '.') . ' (B. Gaji)']);
                            $items = array_merge($items, ['persentase' => $persentaseJS . '%']);
                        } else if ($items['JENIS JASA'] == "JP") {
                            $jumlahKonversi = $persentase * $nominalJP;
                            $items = array_merge($items, ['jumlahSetelahDiKonversi' => $jumlahKonversi]);
                            $items = array_merge($items, ['jumlahHasilPersentase' => $nominalJP]);
                            $items = array_merge($items, ['sisa_nominal' => number_format((intval(intval($items['JUMLAH']) - $jumlahKonversi)), 0, ',', '.') . ' (P. BPJS)']);
                            $items = array_merge($items, ['persentase' => $persentaseJP . '%']);
                        }
                        return $items;
                    });
                }
                $bebanGaji = collect($item)->where('JENIS JASA', 'JS')->all();
                $pendapatanBPJS = collect($item)->where('JENIS JASA', 'JP')->all();
                if (count($bebanGaji) <= 0) {
                    $bebanGaji = $jumlahNominal;
                    $pendapatanBPJS = 0;
                } else
                if (count($pendapatanBPJS) <= 0) {
                    $bebanGaji = 0;
                    $pendapatanBPJS = $jumlahNominal;
                } else {
                    $bebanGaji = collect($bebanGaji)->sum(function ($item) {
                        return intval($item['JUMLAH']);
                    });
                    $pendapatanBPJS = collect($pendapatanBPJS)->sum(function ($item) {
                        return intval($item['JUMLAH']);
                    });
                }

                $rm = $item[0]['RM'];
                $pasien = $item[0]['PASIEN'];

                $tarifBPJS = $dataBPJSRJ->where('No_RM', $rm)->pluck('Total Tarif')->first();
                $inacbg = $dataBPJSRJ->where('No_RM', $rm)->pluck('INACBG')->first();

                $dataKonversi = $item->map(function ($items) use ($tarifBPJS, $jumlahNominal) {
                    $persentase = (intval($items['jumlahSetelahDiKonversi']) / $jumlahNominal) * 100;
                    $jumlahKonversi = ($persentase / 100) * $tarifBPJS;
                    return [
                        'JUMLAH' => intval($items['jumlahSetelahDiKonversi']),
                        'Persentase' => $persentase,
                        'Jumlah_Konversi' => $jumlahKonversi
                    ];
                })->values()->all();

                $array = $array + ['RM' => $rm];
                $array = $array + ['PASIEN' => $pasien];
                $array = $array + ['tarifBPJS' => $tarifBPJS];
                $array = $array + ['INACBG' => $inacbg];
                $array = $array + ['Beban Gaji' => $bebanGaji];
                $array = $array + ['Pendapatan BPJS' => $pendapatanBPJS];
                $array = $array + ['Jumlah Nominal' => $jumlahNominal];
                $array = $array + ['Data Konversi' => $dataKonversi];
                $array = $array + ['data' => $item->values()->all()];
                return  $array;
            });
            $updatedArray = $array_BPJS_RJWithJenisJasa->values()->all();
            Session::put('dataPasienDistributionBPJSRJ', $updatedArray);


            $array_PiutangBPJS_RJ = $this->getPiutangBPJS($pathPendapatanRJ, $pathBPJSRJ);
            $totalJumlahData_JP = 0;
            $totalJumlahData_JS = 0;
            $response = $response + [
                'pasienBPJS_RJ' => $updatedArray,
                'totalJPRJ' => $totalJumlahData_JP,
                'totalJSRJ' => $totalJumlahData_JS,
                'piutangBPJS_RJ' => $array_PiutangBPJS_RJ
            ];
        } else {
            return redirect()->back();
        }
        return Inertia::render('DataDistribution', [
            'data' => $response
        ]);
    }
    public function distributionAll()
    {
        $pathPendapatanRI = Session::get('pathPendapatanRI');
        $pathPendapatanRJ = Session::get('pathPendapatanRJ');
        $pathBPJSRI = Session::get('pathBPJSRI');
        $pathBPJSRJ = Session::get('pathBPJSRJ');
        $response = [];
        if (!Session::get('dataPasienDistributionBPJSRJ') && !Session::get('dataPasienDistributionBPJSRI')) {
            return redirect()->back();
        } else {
            if ($pathPendapatanRI && $pathBPJSRI) {
                // perhitungan pasien non bpjs
                $pasienNonBPJS = $this->getPasienNonBPJS($pathPendapatanRI, $pathBPJSRI);

                $breakdownRINonBPJS = $pasienNonBPJS->groupBy('KLS TARIF')->map(function ($items, $kelasTarif) use ($pasienNonBPJS) {
                    $dataPasien = collect($pasienNonBPJS->where('KLS TARIF', trim($kelasTarif))->values()->all());
                    $jumlah = $dataPasien->reduce(function ($carry, $item) {
                        return $carry + intval($item['JUMLAH']);
                    }, 0);
                    $dataKategoriPendapatanId = JenisJasaAkun::where('kelas_tarif', trim($kelasTarif))->first()['kategori_pendapatans_id'];
                    return [
                        'KLS TARIF' => $kelasTarif,
                        'jumlah' => $jumlah,
                        'kategori_pendapatanId' => $dataKategoriPendapatanId
                    ];
                })->filter(function ($item) {
                    return isset($item);
                })->values()->all();
                $kategoriPendapatan = collect(KategoriPendapatan::all());
                $groupedData = collect($breakdownRINonBPJS)->groupBy(function ($item) use ($kategoriPendapatan) {
                    $kategoriId = $item['kategori_pendapatanId'];
                    $kategori = $kategoriPendapatan->firstWhere('id', $kategoriId);

                    return $kategori ? $kategori->kategori : null;
                })->map(function ($items, $kategori) {
                    $jumlah = collect($items)->reduce(function ($carry, $item) {
                        return $carry + $item['jumlah'];
                    }, 0);

                    return [
                        'kategori' => $kategori,
                        'jumlah' => $jumlah,
                    ];
                })->values()->all();
                $laporanNonBPJS = $groupedData;
                $totalJumlahKonversiNonBPJS = array_reduce($laporanNonBPJS, function ($carry, $items) {
                    return $carry + $items['jumlah'];
                }, 0);
                // akhir dari perhitungan pasien non bpjs

                //perhitunga pasien bpjs
                $dataBPJSRI = collect($this->getDataBpjs($pathBPJSRI, 'RI'));

                $persentaseJP = PercentageJsJp::where('user_id', Auth::id())->first()['jp'];
                $persentaseJS = PercentageJsJp::where('user_id', Auth::id())->first()['js'];

                // PASIEN BPJS
                $array_BPJS_RI = $this->getPasienBPJS($pathPendapatanRI, $pathBPJSRI);
                $kelasTarifMapping = collect(JenisJasaAkun::where('user_id', Auth::id())->get());
                $array_BPJS_RIWithJenisJasa = $array_BPJS_RI->map(function ($item) use ($kelasTarifMapping, $array_BPJS_RI, $persentaseJP, $persentaseJS) {
                    $kelasTarif = trim($item['KLS TARIF']);
                    $jenisJasa = $kelasTarifMapping->where('kelas_tarif', $kelasTarif)->first()['jenis_jasa'];
                    $jumlahNominalPerPasien = collect($array_BPJS_RI)->where('RM', $item['RM'])->all();
                    $jumlahNominalPerPasien = collect($jumlahNominalPerPasien)->sum(function ($items) {
                        return intval($items['JUMLAH']);
                    });

                    // Simpan hasil penggabungan ke dalam variabel $item
                    $item = array_merge($item, ['JENIS JASA' => $jenisJasa]);
                    $item = array_merge($item, ['JUMLAH NOMINAL' => $jumlahNominalPerPasien]);

                    return $item;
                });

                $array_BPJS_RIWithJenisJasa = $array_BPJS_RIWithJenisJasa->groupBy('PASIEN')->map(function ($item) use ($dataBPJSRI, $persentaseJP, $persentaseJS, $array_BPJS_RIWithJenisJasa) {
                    $array_BPJS_RIWithJenisJasaJS = collect($array_BPJS_RIWithJenisJasa->where('RM', $item[0]['RM'])->where('JENIS JASA', 'JS')->all());
                    $array_BPJS_RIWithJenisJasaJP = collect($array_BPJS_RIWithJenisJasa->where('RM', $item[0]['RM'])->where('JENIS JASA', 'JP')->all());
                    $jumlahNominal = $item->sum(function ($items) {
                        return intval($items['JUMLAH']);
                    });

                    $rm = $item[0]['RM'];
                    $pasien = $item[0]['PASIEN'];

                    $tarifBPJS = $dataBPJSRI->where('No_RM', $rm)->pluck('Total Tarif')->first();
                    $inacbg = $dataBPJSRI->where('No_RM', $rm)->pluck('INACBG')->first();

                    $array = [];
                    $nominalJP = (($jumlahNominal * $persentaseJP) / 100);
                    $nominalJS = (($jumlahNominal * $persentaseJS) / 100);
                    if (count($array_BPJS_RIWithJenisJasaJS) == 0) {
                        $item = $item->map(function ($items) use ($jumlahNominal, $nominalJP, $persentaseJP, $persentaseJS, $tarifBPJS) {
                            $persentase = floatval($items['JUMLAH']) / $jumlahNominal;
                            if ($items['JENIS JASA'] == "JP") {
                                $jumlahKonversi = $persentase * $nominalJP;
                                $persentase = (intval($jumlahKonversi) / $jumlahNominal) * 100;
                                $jumlahKonversi2 = ($persentase / 100) * $tarifBPJS;
                                $items = array_merge($items, ['jumlahSetelahDistribution' => $jumlahKonversi]);
                                $items = array_merge($items, ['jumlahSetelahDiKonversi' => $jumlahKonversi2]);
                                $items = array_merge($items, ['jumlahHasilPersentase' => $nominalJP]);
                                $items = array_merge($items, ['sisa_nominal' => number_format((intval(intval($items['JUMLAH']) - $jumlahKonversi)), 0, ',', '.') . ' (B. Gaji)']);
                                $items = array_merge($items, ['persentase' => $persentaseJP . '%']);
                            }
                            return $items;
                        });
                    }
                    if (count($array_BPJS_RIWithJenisJasaJP) == 0) {
                        $item = $item->map(function ($items) use ($jumlahNominal, $nominalJS, $persentaseJS, $tarifBPJS) {
                            $persentase = floatval($items['JUMLAH']) / $jumlahNominal;
                            if ($items['JENIS JASA'] == "JS") {
                                $jumlahKonversi = $persentase * $nominalJS;
                                $persentase = (intval($jumlahKonversi) / $jumlahNominal) * 100;
                                $jumlahKonversi2 = ($persentase / 100) * $tarifBPJS;
                                $items = array_merge($items, ['jumlahSetelahDistribution' => $jumlahKonversi]);
                                $items = array_merge($items, ['jumlahSetelahDiKonversi' => $jumlahKonversi2]);
                                $items = array_merge($items, ['jumlahHasilPersentase' => $nominalJS]);
                                $items = array_merge($items, ['sisa_nominal' => number_format((intval(intval($items['JUMLAH']) - $jumlahKonversi)), 0, ',', '.') . ' (P. BPJS)']);
                                $items = array_merge($items, ['persentase' => $persentaseJS . '%']);
                            }
                            return $items;
                        });
                    } else {
                        $item = $item->map(function ($items) use ($jumlahNominal, $nominalJS, $nominalJP, $persentaseJP, $persentaseJS, $tarifBPJS) {
                            $persentase = floatval($items['JUMLAH']) / $jumlahNominal;
                            if ($items['JENIS JASA'] == "JS") {
                                $jumlahKonversi = $persentase * $nominalJS;
                                $persentase = (intval($jumlahKonversi) / $jumlahNominal) * 100;
                                $jumlahKonversi2 = ($persentase / 100) * $tarifBPJS;
                                $items = array_merge($items, ['jumlahSetelahDistribution' => $jumlahKonversi]);
                                $items = array_merge($items, ['jumlahSetelahDiKonversi' => $jumlahKonversi2]);
                                $items = array_merge($items, ['jumlahHasilPersentase' => $nominalJS]);
                                $items = array_merge($items, ['sisa_nominal' => number_format((intval(intval($items['JUMLAH']) - $jumlahKonversi)), 0, ',', '.') . ' (B. Gaji)']);
                                $items = array_merge($items, ['persentase' => $persentaseJS . '%']);
                            } else if ($items['JENIS JASA'] == "JP") {
                                $jumlahKonversi = $persentase * $nominalJP;
                                $persentase = (intval($jumlahKonversi) / $jumlahNominal) * 100;
                                $jumlahKonversi2 = ($persentase / 100) * $tarifBPJS;
                                $items = array_merge($items, ['jumlahSetelahDistribution' => $jumlahKonversi]);
                                $items = array_merge($items, ['jumlahSetelahDiKonversi' => $jumlahKonversi2]);
                                $items = array_merge($items, ['jumlahHasilPersentase' => $nominalJP]);
                                $items = array_merge($items, ['sisa_nominal' => number_format((intval(intval($items['JUMLAH']) - $jumlahKonversi)), 0, ',', '.') . ' (P. BPJS)']);
                                $items = array_merge($items, ['persentase' => $persentaseJP . '%']);
                            }
                            return $items;
                        });
                    }
                    $bebanGaji = collect($item)->where('JENIS JASA', 'JS')->all();
                    $pendapatanBPJS = collect($item)->where('JENIS JASA', 'JP')->all();
                    if (count($bebanGaji) <= 0) {
                        $bebanGaji = $jumlahNominal;
                        $pendapatanBPJS = 0;
                    } else
                if (count($pendapatanBPJS) <= 0) {
                        $bebanGaji = 0;
                        $pendapatanBPJS = $jumlahNominal;
                    } else {
                        $bebanGaji = collect($bebanGaji)->sum(function ($item) {
                            return intval($item['JUMLAH']);
                        });
                        $pendapatanBPJS = collect($pendapatanBPJS)->sum(function ($item) {
                            return intval($item['JUMLAH']);
                        });
                    }

                    $array = $array + ['RM' => $rm];
                    $array = $array + ['PASIEN' => $pasien];
                    $array = $array + ['tarifBPJS' => $tarifBPJS];
                    $array = $array + ['INACBG' => $inacbg];
                    $array = $array + ['Beban Gaji' => $bebanGaji];
                    $array = $array + ['Pendapatan BPJS' => $pendapatanBPJS];
                    $array = $array + ['Jumlah Nominal' => $jumlahNominal];
                    $array = $array + ['data' => $item->values()->all()];
                    return  $array;
                });
                $updatedArray = $array_BPJS_RIWithJenisJasa->values()->all();
                $updatedArray = $array_BPJS_RIWithJenisJasa->flatMap(function ($item) {
                    $data = collect($item['data'])->map(function ($dataItem) use ($item) {
                        $dataItem['RM'] = $item['RM'];
                        $dataItem['PASIEN'] = $item['PASIEN'];
                        $dataItem['tarifBPJS'] = $item['tarifBPJS'];
                        $dataItem['INACBG'] = $item['INACBG'];
                        $dataItem['Beban Gaji'] = $item['Beban Gaji'];
                        $dataItem['Pendapatan BPJS'] = $item['Pendapatan BPJS'];
                        $dataItem['Jumlah Nominal'] = $item['Jumlah Nominal'];
                        return $dataItem;
                    });

                    return $data;
                })->values()->all();


                $breakdownRIBPJS = collect($updatedArray)->groupBy('KLS TARIF')->map(function ($items) {
                    $dataKategoriPendapatanId = JenisJasaAkun::where('kelas_tarif', trim($items[0]['KLS TARIF']))->first()['kategori_pendapatans_id'];
                    $jumlah =   $items->sum(function ($item) {
                        return intval($item['JUMLAH']);
                    });
                    return [
                        "KLS TARIF" => $items[0]['KLS TARIF'],
                        "jumlah" => $jumlah,
                        'kategori_pendapatanId' => $dataKategoriPendapatanId
                    ];
                })->filter(function ($item) {
                    return isset($item);
                })->values()->all();
                $kategoriPendapatan = collect(KategoriPendapatan::all());
                $groupedData = collect($breakdownRIBPJS)->groupBy(function ($item) use ($kategoriPendapatan) {
                    $kategoriId = $item['kategori_pendapatanId'];
                    $kategori = $kategoriPendapatan->firstWhere('id', $kategoriId);

                    return $kategori ? $kategori->kategori : null;
                })->map(function ($items, $kategori) {
                    $jumlah = collect($items)->reduce(function ($carry, $item) {
                        return $carry + $item['jumlah'];
                    }, 0);

                    return [
                        'kategori' => $kategori,
                        'jumlah' => $jumlah,
                    ];
                })->values()->all();
                $laporanBPJS = $groupedData;

                $totalJumlahKonversiBPJS = array_reduce($laporanBPJS, function ($carry, $items) {
                    return $carry + $items['jumlah'];
                }, 0);
                // akhir dari perhitungan pasien bpjs

                $laporanRI = array_merge_recursive($laporanNonBPJS, $laporanBPJS);

                $laporanRI = collect($laporanRI)->groupBy('kategori')->map(function ($items) {
                    $jumlah = $items->sum('jumlah');
                    return [
                        'kategori' => $items[0]['kategori'],
                        'jumlah' => intval($jumlah)
                    ];
                })->values()->all();


                $response = $response + [
                    'pendapatanRINonBPJS' => intval($totalJumlahKonversiNonBPJS),
                    'pendapatanRIBPJS' => intval($totalJumlahKonversiBPJS),
                    'laporanRI' => $laporanRI
                ];
            }
            if ($pathPendapatanRJ && $pathBPJSRJ) {

                // perhitungan pasien non bpjs
                $pasienNonBPJS = $this->getPasienNonBPJS($pathPendapatanRJ, $pathBPJSRJ);
                $breakdownRJNonBPJS = $pasienNonBPJS->groupBy('KLS TARIF')->map(function ($items, $kelasTarif) use ($pasienNonBPJS) {
                    $dataPasien = collect($pasienNonBPJS->where('KLS TARIF', trim($kelasTarif))->values()->all());
                    $jumlah = $dataPasien->reduce(function ($carry, $item) {
                        return $carry + intval($item['JUMLAH']);
                    }, 0);
                    $dataKategoriPendapatanId = JenisJasaAkun::where('kelas_tarif', trim($kelasTarif))->first()['kategori_pendapatans_id'];
                    return [
                        'KLS TARIF' => $kelasTarif,
                        'jumlah' => $jumlah,
                        'kategori_pendapatanId' => $dataKategoriPendapatanId
                    ];
                })->filter(function ($item) {
                    return isset($item);
                })->values()->all();
                $kategoriPendapatan = collect(KategoriPendapatan::all());
                $groupedData = collect($breakdownRJNonBPJS)->groupBy(function ($item) use ($kategoriPendapatan) {
                    $kategoriId = $item['kategori_pendapatanId'];
                    $kategori = $kategoriPendapatan->firstWhere('id', $kategoriId);

                    return $kategori ? $kategori->kategori : null;
                })->map(function ($items, $kategori) {
                    $jumlah = collect($items)->reduce(function ($carry, $item) {
                        return $carry + $item['jumlah'];
                    }, 0);

                    return [
                        'kategori' => $kategori,
                        'jumlah' => $jumlah,
                    ];
                })->values()->all();
                $laporanNonBPJS = $groupedData;
                $totalJumlahKonversiNonBPJS = array_reduce($laporanNonBPJS, function ($carry, $items) {
                    return $carry + $items['jumlah'];
                }, 0);
                // akhir dari perhitungan pasien non bpjs


                $dataBPJSRJ = collect($this->getDataBpjs($pathBPJSRJ, 'RJ'));
                // PASIEN BPJS
                $array_BPJS_RJ = $this->getPasienBPJS($pathPendapatanRJ, $pathBPJSRJ);
                $kelasTarifMapping = collect(JenisJasaAkun::where('user_id', Auth::id())->get());
                $array_BPJS_RJWithJenisJasa = $array_BPJS_RJ->map(function ($item) use ($kelasTarifMapping, $array_BPJS_RJ, $persentaseJP, $persentaseJS) {
                    $kelasTarif = trim($item['KLS TARIF']);
                    $jenisJasa = $kelasTarifMapping->where('kelas_tarif', $kelasTarif)->first()['jenis_jasa'];
                    $jumlahNominalPerPasien = collect($array_BPJS_RJ)->where('RM', $item['RM'])->all();
                    $jumlahNominalPerPasien = collect($jumlahNominalPerPasien)->sum(function ($items) {
                        return intval($items['JUMLAH']);
                    });

                    // Simpan hasil penggabungan ke dalam variabel $item
                    $item = array_merge($item, ['JENIS JASA' => $jenisJasa]);
                    $item = array_merge($item, ['JUMLAH NOMINAL' => $jumlahNominalPerPasien]);

                    return $item;
                });

                $array_BPJS_RJWithJenisJasa = $array_BPJS_RJWithJenisJasa->groupBy('PASIEN')->map(function ($item) use ($dataBPJSRJ, $persentaseJP, $persentaseJS, $array_BPJS_RJWithJenisJasa) {
                    $array_BPJS_RJWithJenisJasaJS = collect($array_BPJS_RJWithJenisJasa->where('RM', $item[0]['RM'])->where('JENIS JASA', 'JS')->all());
                    $array_BPJS_RJWithJenisJasaJP = collect($array_BPJS_RJWithJenisJasa->where('RM', $item[0]['RM'])->where('JENIS JASA', 'JP')->all());
                    $jumlahNominal = $item->sum(function ($items) {
                        return intval($items['JUMLAH']);
                    });

                    $rm = $item[0]['RM'];
                    $pasien = $item[0]['PASIEN'];

                    $tarifBPJS = $dataBPJSRJ->where('No_RM', $rm)->pluck('Total Tarif')->first();
                    $inacbg = $dataBPJSRJ->where('No_RM', $rm)->pluck('INACBG')->first();

                    $array = [];
                    $nominalJP = (($jumlahNominal * $persentaseJP) / 100);
                    $nominalJS = (($jumlahNominal * $persentaseJS) / 100);
                    if (count($array_BPJS_RJWithJenisJasaJS) == 0) {
                        $item = $item->map(function ($items) use ($jumlahNominal, $nominalJP, $persentaseJP, $persentaseJS, $tarifBPJS) {
                            $persentase = floatval($items['JUMLAH']) / $jumlahNominal;
                            if ($items['JENIS JASA'] == "JP") {
                                $jumlahKonversi = $persentase * $nominalJP;
                                $persentase = (intval($jumlahKonversi) / $jumlahNominal) * 100;
                                $jumlahKonversi2 = ($persentase / 100) * $tarifBPJS;
                                $items = array_merge($items, ['jumlahSetelahDistribution' => $jumlahKonversi]);
                                $items = array_merge($items, ['jumlahSetelahDiKonversi' => $jumlahKonversi2]);
                                $items = array_merge($items, ['jumlahHasilPersentase' => $nominalJP]);
                                $items = array_merge($items, ['sisa_nominal' => number_format((intval(intval($items['JUMLAH']) - $jumlahKonversi)), 0, ',', '.') . ' (B. Gaji)']);
                                $items = array_merge($items, ['persentase' => $persentaseJP . '%']);
                            }
                            return $items;
                        });
                    }
                    if (count($array_BPJS_RJWithJenisJasaJP) == 0) {
                        $item = $item->map(function ($items) use ($jumlahNominal, $nominalJS, $persentaseJS, $tarifBPJS) {
                            $persentase = floatval($items['JUMLAH']) / $jumlahNominal;
                            if ($items['JENIS JASA'] == "JS") {
                                $jumlahKonversi = $persentase * $nominalJS;
                                $persentase = (intval($jumlahKonversi) / $jumlahNominal) * 100;
                                $jumlahKonversi2 = ($persentase / 100) * $tarifBPJS;
                                $items = array_merge($items, ['jumlahSetelahDistribution' => $jumlahKonversi]);
                                $items = array_merge($items, ['jumlahSetelahDiKonversi' => $jumlahKonversi2]);
                                $items = array_merge($items, ['jumlahHasilPersentase' => $nominalJS]);
                                $items = array_merge($items, ['sisa_nominal' => number_format((intval(intval($items['JUMLAH']) - $jumlahKonversi)), 0, ',', '.') . ' (P. BPJS)']);
                                $items = array_merge($items, ['persentase' => $persentaseJS . '%']);
                            }
                            return $items;
                        });
                    } else {
                        $item = $item->map(function ($items) use ($jumlahNominal, $nominalJS, $nominalJP, $persentaseJP, $persentaseJS, $tarifBPJS) {
                            $persentase = floatval($items['JUMLAH']) / $jumlahNominal;
                            if ($items['JENIS JASA'] == "JS") {
                                $jumlahKonversi = $persentase * $nominalJS;
                                $persentase = (intval($jumlahKonversi) / $jumlahNominal) * 100;
                                $jumlahKonversi2 = ($persentase / 100) * $tarifBPJS;
                                $items = array_merge($items, ['jumlahSetelahDistribution' => $jumlahKonversi]);
                                $items = array_merge($items, ['jumlahSetelahDiKonversi' => $jumlahKonversi2]);
                                $items = array_merge($items, ['jumlahHasilPersentase' => $nominalJS]);
                                $items = array_merge($items, ['sisa_nominal' => number_format((intval(intval($items['JUMLAH']) - $jumlahKonversi)), 0, ',', '.') . ' (B. Gaji)']);
                                $items = array_merge($items, ['persentase' => $persentaseJS . '%']);
                            } else if ($items['JENIS JASA'] == "JP") {
                                $jumlahKonversi = $persentase * $nominalJP;
                                $persentase = (intval($jumlahKonversi) / $jumlahNominal) * 100;
                                $jumlahKonversi2 = ($persentase / 100) * $tarifBPJS;
                                $items = array_merge($items, ['jumlahSetelahDistribution' => $jumlahKonversi]);
                                $items = array_merge($items, ['jumlahSetelahDiKonversi' => $jumlahKonversi2]);
                                $items = array_merge($items, ['jumlahHasilPersentase' => $nominalJP]);
                                $items = array_merge($items, ['sisa_nominal' => number_format((intval(intval($items['JUMLAH']) - $jumlahKonversi)), 0, ',', '.') . ' (P. BPJS)']);
                                $items = array_merge($items, ['persentase' => $persentaseJP . '%']);
                            }
                            return $items;
                        });
                    }
                    $bebanGaji = collect($item)->where('JENIS JASA', 'JS')->all();
                    $pendapatanBPJS = collect($item)->where('JENIS JASA', 'JP')->all();
                    if (count($bebanGaji) <= 0) {
                        $bebanGaji = $jumlahNominal;
                        $pendapatanBPJS = 0;
                    } else
                if (count($pendapatanBPJS) <= 0) {
                        $bebanGaji = 0;
                        $pendapatanBPJS = $jumlahNominal;
                    } else {
                        $bebanGaji = collect($bebanGaji)->sum(function ($item) {
                            return intval($item['JUMLAH']);
                        });
                        $pendapatanBPJS = collect($pendapatanBPJS)->sum(function ($item) {
                            return intval($item['JUMLAH']);
                        });
                    }

                    $array = $array + ['RM' => $rm];
                    $array = $array + ['PASIEN' => $pasien];
                    $array = $array + ['tarifBPJS' => $tarifBPJS];
                    $array = $array + ['INACBG' => $inacbg];
                    $array = $array + ['Beban Gaji' => $bebanGaji];
                    $array = $array + ['Pendapatan BPJS' => $pendapatanBPJS];
                    $array = $array + ['Jumlah Nominal' => $jumlahNominal];
                    $array = $array + ['data' => $item->values()->all()];
                    return  $array;
                });
                $updatedArray = $array_BPJS_RJWithJenisJasa->values()->all();
                $updatedArray = $array_BPJS_RJWithJenisJasa->flatMap(function ($item) {
                    $data = collect($item['data'])->map(function ($dataItem) use ($item) {
                        $dataItem['RM'] = $item['RM'];
                        $dataItem['PASIEN'] = $item['PASIEN'];
                        $dataItem['tarifBPJS'] = $item['tarifBPJS'];
                        $dataItem['INACBG'] = $item['INACBG'];
                        $dataItem['Beban Gaji'] = $item['Beban Gaji'];
                        $dataItem['Pendapatan BPJS'] = $item['Pendapatan BPJS'];
                        $dataItem['Jumlah Nominal'] = $item['Jumlah Nominal'];
                        return $dataItem;
                    });

                    return $data;
                })->values()->all();


                $breakdownRIBPJS = collect($updatedArray)->groupBy('KLS TARIF')->map(function ($items) {
                    $dataKategoriPendapatanId = JenisJasaAkun::where('kelas_tarif', trim($items[0]['KLS TARIF']))->first()['kategori_pendapatans_id'];
                    $jumlah =   $items->sum(function ($item) {
                        return intval($item['JUMLAH']);
                    });
                    return [
                        "KLS TARIF" => $items[0]['KLS TARIF'],
                        "jumlah" => $jumlah,
                        'kategori_pendapatanId' => $dataKategoriPendapatanId
                    ];
                })->filter(function ($item) {
                    return isset($item);
                })->values()->all();
                $kategoriPendapatan = collect(KategoriPendapatan::all());
                $groupedData = collect($breakdownRIBPJS)->groupBy(function ($item) use ($kategoriPendapatan) {
                    $kategoriId = $item['kategori_pendapatanId'];
                    $kategori = $kategoriPendapatan->firstWhere('id', $kategoriId);

                    return $kategori ? $kategori->kategori : null;
                })->map(function ($items, $kategori) {
                    $jumlah = collect($items)->reduce(function ($carry, $item) {
                        return $carry + $item['jumlah'];
                    }, 0);

                    return [
                        'kategori' => $kategori,
                        'jumlah' => $jumlah,
                    ];
                })->values()->all();
                $laporanBPJS = $groupedData;

                $totalJumlahKonversiBPJS = array_reduce($laporanBPJS, function ($carry, $items) {
                    return $carry + $items['jumlah'];
                }, 0);


                $laporanRJ = array_merge_recursive($laporanNonBPJS, $laporanBPJS);

                $laporanRJ = collect($laporanRJ)->groupBy('kategori')->map(function ($items) {
                    $jumlah = $items->sum('jumlah');
                    return [
                        'kategori' => $items[0]['kategori'],
                        'jumlah' => intval($jumlah)
                    ];
                })->values()->all();

                $response = $response + [
                    'pendapatanRJNonBPJS' => intval($totalJumlahKonversiNonBPJS),
                    'pendapatanRJBPJS' => intval( $totalJumlahKonversiBPJS),
                    'laporanRJ' => $laporanRJ
                ];
            }
        }


        return Inertia::render('DistributionReportAll', [
            'data' => $response
        ]);
    }
}
