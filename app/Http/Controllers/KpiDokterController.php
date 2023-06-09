<?php

namespace App\Http\Controllers;

use App\Models\BobotKPI;
use App\Models\DataDokter;
use App\Models\KpiDokter;
use App\Models\KpiKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class KpiDokterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id();
        $data_dokter = DataDokter::where('user_id', $userId)->get();
        $kpi_dokter = KpiDokter::where('user_id', $userId)->get(); 
        $kpi_kategori = BobotKPI::where('user_id', $userId)->get();
        $kategori2 = KpiKategori::all();
        
        $dataKategori = [];
        foreach ($kpi_kategori as $kategori) {
            $dataKategori[] =  [
                'id' => $kategori->id,
                'dataKategori' => $kategori->dataKategori->kategori,
                'bobot' => $kategori->bobot
            ];
        }
        // dd( $kpi_dokter[0]['kpiKategori']);
        // die;
        return Inertia::render('KpiDokter', [
            'data_dokter' => $data_dokter,
            'kpi_dokter' => $kpi_dokter,
            'kpi_kategori' => $kategori2,
            'dataKategori' => $dataKategori

        ]);
    }
    
    public function getTemplateKPI()
    {
        $userId = Auth::id();
        $data = BobotKPI::where('user_id', $userId)->get();
        if (count($data) > 0) {
            return redirect()->back();
        } else{
            $data = KpiKategori::all();
            foreach ($data as $key) {
                $kpi = new BobotKPI();
                $kpi->user_id = $userId;
                $kpi->bobot = 1;
                $kpi->kategori_id = $key->id;
                $kpi->save();
            }
        }
        return redirect()->back();
    }
    public function kpiKategoriUpdate(Request $request)
    {
        $kategoriKpi = BobotKPI::find($request->input('id'));
        $kategoriKpi->bobot = $request->input('bobot');
        $kategoriKpi->save();
        $dataKategori = [];
        $kpi_kategori = BobotKPI::where('user_id', Auth::id())->get();
        foreach ($kpi_kategori as $kategori) {
            $dataKategori[] =  [
                'id' => $kategori->id,
                'dataKategori' => $kategori->dataKategori->kategori,
                'bobot' => $kategori->bobot
            ];
        }
        return response()->json([
            'dataKategori' => $dataKategori
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $kategoriKpi = new KpiDokter();
        $kategoriKpi->kategori_id = $request->input('kategori');
        $kategoriKpi->kelompok = $request->input('kelompok');
        $kategoriKpi->nilai = $request->input('nilai');
        $kategoriKpi->user_id = Auth::id();
        $kategoriKpi->save();
        $kpi_dokter = KpiDokter::where('user_id', Auth::id())->get(); 
        return response()->json([
            'kpi_dokter' =>$kpi_dokter 
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(KpiDokter $kpiDokter)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KpiDokter $kpiDokter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $kategoriKpi = KpiDokter::find($request->input('id'));
        $kategoriKpi->kelompok = $request->input('kelompok');
        $kategoriKpi->nilai = $request->input('nilai');
        $kategoriKpi->save();
        $kpi_dokter = KpiDokter::where('user_id', Auth::id())->get(); 
        return response()->json([
            'kpi_dokter' =>$kpi_dokter 
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $kpiDokter)
    {
        $vendor = KpiDokter::find($kpiDokter->input('id'));
        $vendor->delete();

        // Tambahkan logika lainnya jika diperlukan

        $kpi_dokter = KpiDokter::where('user_id', Auth::id())->get(); 
        return response()->json([
            'kpi_dokter' =>$kpi_dokter 
        ]);
    }
    public function kpiDokters()
    {
        die;
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

        $headers = ['RM', 'NOTRANS', 'TANGGAL', 'PASIEN', 'UNIT', 'FAKTUR', 'PRODUK', 'KLS TARIF', 'OBAT', 'QTY', 'TARIP', 'JUMLAH', 'DOKTER', 'PENJAMIN', 'INVOICE', 'BAYAR', 'NIP'];
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
