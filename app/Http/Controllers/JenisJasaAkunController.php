<?php

namespace App\Http\Controllers;

use App\Models\JenisJasaAkun;
use App\Models\TemplateKelasTarif;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class JenisJasaAkunController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id();
        $data = JenisJasaAkun::where('user_id', $userId)->get();
        $data_template = TemplateKelasTarif::all();
        return Inertia::render('SetServiceType', [
            'data' => $data,
            'data_template' => $data_template
        ]);
    }
    public function get()
    {
        $userId = Auth::id();
        $data = JenisJasaAkun::where('user_id', $userId)->get();
        $response = [
            'data' => $data
        ];
        return response()->json($response);
    }

    public function useTemplate(Request $template)
    {
        $template = $template->input('template');

        // Ambil data dari TemplateKelasTarif dengan template yang sesuai
        $templateData = TemplateKelasTarif::where('template', $template)->get();

        // Periksa apakah data ditemukan
        if ($templateData->isEmpty()) {
            return response()->json(['message' => 'Data template tidak ditemukan'], 404);
        }

        $userId = Auth::id();

        // Tambahkan data ke tabel/model JenisJasaAkun
        foreach ($templateData as $item) {
            $serviceType = new JenisJasaAkun();
            $serviceType->user_id = $userId;
            $serviceType->kelas_tarif = $item->kelas_tarif;
            $serviceType->jenis_jasa = $item->jenis_jasa;
            $serviceType->save();

        }

        
        $data = JenisJasaAkun::where('user_id', $userId)->get();
        $response = [
            'data' => $data
        ];

        return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kelas_tarif' => 'required',
            'jenis_jasa' => 'required'
        ]);

        // Membuat objek ServiceType baru
        $serviceType = new JenisJasaAkun();
        $serviceType->kelas_tarif = $request->kelas_tarif;
        $serviceType->jenis_jasa = $request->jenis_jasa;
        $serviceType->user_id = Auth::id();
        $serviceType->save();

        // Mengembalikan respons dengan data yang baru ditambahkan
        return response()->json(['data' => $serviceType], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(JenisJasaAkun $jenisJasaAkun)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function destroy($id)
    {
        $serviceType = JenisJasaAkun::findOrFail($id);
        $serviceType->delete();

        $userId = Auth::id();
        $data = JenisJasaAkun::where('user_id', $userId)->get();

        $response = [
            'data' => $data
        ];
        return response()->json($response);
    }

    public function edit($id)
    {
        $serviceType = JenisJasaAkun::findOrFail($id);
        return redirect()->back()->with('success', 'Data jenis jasa berhasil dihapus.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JenisJasaAkun $jenisJasaAkun)
    {
        //
    }
}
