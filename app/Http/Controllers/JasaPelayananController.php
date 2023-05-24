<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\JasaPelayanan;
use App\Models\PercentageJsJp;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JasaPelayananController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $userId = Auth::id();
        $data = PercentageJsJp::where('user_id', $userId)->get();
        return Inertia::render('JasaPelayanan', [
            'data' => $data
        ]);
    }
    public function submitPercentage(Request $request)
{
    // Memvalidasi input persentase
    $request->validate([
        'percentage' => 'required|numeric|between:1,100'
    ]);

    $userId = Auth::id();

    // Mencari data persentase berdasarkan ID pengguna yang sedang login
    $percentage = PercentageJsJp::where('user_id', $userId)->first();

    if ($percentage) {
        // Jika data persentase sudah ada, lakukan operasi update
        $percentage->update([
            'js' => 100 - $request->percentage,
            'jp' => $request->percentage
        ]);

        // Berikan respons sesuai kebutuhan Anda (misalnya, berhasil diperbarui)
        return response()->json(['data' => $request->percentage]);
    } else {
        // Jika data persentase belum ada, lakukan operasi create
        PercentageJsJp::create([
            'js' => 100 - $request->percentage,
            'jp' => $request->percentage
        ]);

        // Berikan respons sesuai kebutuhan Anda (misalnya, berhasil dibuat)
        return response()->json(['data' => $request->percentage]);
    }
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(JasaPelayanan $jasaPelayanan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JasaPelayanan $jasaPelayanan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JasaPelayanan $jasaPelayanan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JasaPelayanan $jasaPelayanan)
    {
        //
    }
}
