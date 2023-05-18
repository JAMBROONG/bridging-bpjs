<?php

namespace App\Http\Controllers;

use App\Models\DataPendapatanRsRi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DataPendapatanRsRiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    public function shiftingShow()
    {
        $users = User::with('dataPendapatanRsRi')->get();
        $dataPendapatans = DataPendapatanRsRi::with('user')->get();
        return Inertia::render('Shifting/Shifting');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }
    public function upload_shifting(Request $request)
    {
    $request->validate([
        'file1' => 'required|mimes:xlsx,xls',
        'file2' => 'required|mimes:xlsx,xls',
    ]);

    try {
        if ($request->hasFile('file1') && $request->hasFile('file2')) {
            $file1 = $request->file('file1');
            $file2 = $request->file('file2');

            // Simpan file ke direktori penyimpanan (storage)
            $file1Path = $file1->store('excel');
            $file2Path = $file2->store('excel');



            // Lakukan operasi lain yang Anda butuhkan, misalnya menyimpan data ke database

            // Response berhasil dengan data Excel
            return response()->json([
                'message' => 'File berhasil diunggah'
            ]);
        } else {
            return response()->json([
                'message' => 'File tidak ditemukan',
            ], 400);
        }
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Terjadi kesalahan saat memproses file',
            'error' => $e->getMessage(),
        ], 500);
    }
}
    /**
     * Display the specified resource.
     */
    public function show(DataPendapatanRsRi $dataPendapatanRsRi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataPendapatanRsRi $dataPendapatanRsRi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataPendapatanRsRi $dataPendapatanRsRi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataPendapatanRsRi $dataPendapatanRsRi)
    {
        //
    }
}
