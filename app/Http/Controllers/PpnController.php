<?php

namespace App\Http\Controllers;

use App\Models\JenisJasaAkun;
use App\Models\KategoriPendapatan;
use App\Models\PercentageJsJp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class PpnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        if (empty(PercentageJsJp::where('user_id', Auth::id())->first()['jp'])) {
            return redirect()->back();
        }
        $pathPendapatanRI = Session::get('pathPendapatanRI');
        $pathPendapatanRJ = Session::get('pathPendapatanRJ');
        $pathBPJSRI = Session::get('pathBPJSRI');
        $pathBPJSRJ = Session::get('pathBPJSRJ');

        $data = [
            'pathFilePpn' => Session::get('Ppn'),
            'pathPendapatanRI' =>$pathPendapatanRI,
            'pathPendapatanRJ' =>$pathPendapatanRJ,
            'pathBPJSRI' =>$pathBPJSRI,
            'pathBPJSRJ' =>$pathBPJSRJ
        ];
        if (Session::get('Ppn')) {

            // Read the Excel file and extract data
            $excelData = [];
            $rows = Excel::toArray([], storage_path('app/' . Session::get('Ppn')));
            if (!empty($rows) && count($rows[0]) > 0) {
                $headerRow = $rows[0][0];
                $headerIndex = array_search('JUMLAH_PPN', $headerRow);

                if ($headerIndex !== false) {
                    for ($i = 1; $i < count($rows[0]); $i++) {
                        $row = $rows[0][$i];
                        if (isset($row[$headerIndex])) {
                            $excelData[] = $row[$headerIndex];
                        }
                    }
                }
            }
            $dataPpnShifting = $this->getDataShifting();
            $dataPpnDistribution = $this->getDataDistribution();
            $data = $data + [
                'dataPPNShifting' => $dataPpnShifting,
                'dataPPNDistribution' => $dataPpnDistribution,
                'jumlahPPN' => array_sum($excelData)
            ];
        }
        return Inertia::render('Ppn', [
            'data' => $data
        ]);
    }
    public function delPathFilePpn()
    {
        File::delete(Session::forget('Ppn'));
        Session::forget('Ppn');
        return redirect()->back()->with('message', 'File berhasil dihapus');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Handle file upload and save data to array.
     */
    public function uploadPpn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid file. Please upload an Excel file.'], 422);
        }

        if ($request->hasFile('file')) {
            $filePPN = $request->file('file');
            $pathFilePPN = $filePPN->store('uploads');
            Session::put('Ppn', $pathFilePPN);

            // Read the Excel file and extract data
            $excelData = [];
            $rows = Excel::toArray([], storage_path('app/' . $pathFilePPN));
            if (!empty($rows) && count($rows[0]) > 0) {
                $headerRow = $rows[0][0];
                $headerIndex = array_search('JUMLAH_PPN', $headerRow);

                if ($headerIndex !== false) {
                    for ($i = 1; $i < count($rows[0]); $i++) {
                        $row = $rows[0][$i];
                        if (isset($row[$headerIndex])) {
                            $excelData[] = $row[$headerIndex];
                        }
                    }
                }
            }
            return response()->json(['message' => 'File uploaded successfully.', 'excelData' => array_sum($excelData),'data' => ['pathFilePpn' => Session::get('Ppn')]]);
        }

        return response()->json(['message' => 'No file uploaded.'], 422);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update()
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        //
    }

    public function getDataShifting()
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
            $laporanRI = collect($laporanRI)->where('kategori', 'Obat dan Perlengkapan Medis')->first();
            $response = $response + [
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
            
            $laporanRJ = collect($laporanRJ)->where('kategori', 'Obat dan Perlengkapan Medis')->first();
            $response = $response + [
                'laporanRJ' => $laporanRJ
            ];
        }
        return $response;
    }
    
    public function getDataDistribution()
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
                
            $laporanRI = collect($laporanRI)->where('kategori', 'Obat dan Perlengkapan Medis')->first();
                $response = $response + [
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
                
            $laporanRJ = collect($laporanRJ)->where('kategori', 'Obat dan Perlengkapan Medis')->first();

                $response = $response + [
                    'laporanRJ' => $laporanRJ
                ];
            }
        }


        return  $response;
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
