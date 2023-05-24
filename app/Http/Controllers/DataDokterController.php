<?php

namespace App\Http\Controllers;

use App\Models\DataDokter;
use App\Models\PercentageJlJtl;
use App\Models\PercentageJsJp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DataDokterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id();
        $data = PercentageJsJp::where('user_id', $userId)->get();
        $data_jp = PercentageJlJtl::where('user_id', $userId)->get();
        $data_dokter = DataDokter::where('user_id', $userId)->get();
        return Inertia::render('Dokter', [
            'data' => $data,
            'data_jp' => $data_jp,
            'data_dokter' => $data_dokter
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $userId = Auth::id();
        // Validasi request
        $validatedData = $request->validate([
            'nama_dokter' => 'required|string|max:255',
        ]);

        // Buat instance DataDokter baru
        $dataDokter = new DataDokter();
        $dataDokter->user_id = $userId;
        $dataDokter->nama_dokter = $validatedData['nama_dokter'];

        // Simpan data dokter ke database
        $dataDokter->save();

        // Berikan respons dengan data dokter yang baru ditambahkan
        return response()->json([
            'dokter' => $dataDokter,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(DataDokter $dataDokter)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataDokter $dataDokter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataDokter $dataDokter)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $dataDokter = DataDokter::findOrFail($id);
        $dataDokter->delete();
        $userId = Auth::id();
        $data = DataDokter::where('user_id', $userId)->get();
        $response = [
            'dokter' => $data
        ];
        return response()->json($response,200);
    }
}
