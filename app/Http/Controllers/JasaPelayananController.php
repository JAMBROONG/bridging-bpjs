<?php

namespace App\Http\Controllers;

use App\Models\DataDokter;
use Illuminate\Support\Facades\Auth;
use App\Models\JasaPelayanan;
use App\Models\PercentageJlJtl;
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
        $data_jp = PercentageJlJtl::where('user_id', $userId)->get();
        $data_dokter = DataDokter::where('user_id', $userId)->get();
        return Inertia::render('JasaPelayanan', [
            'data' => $data,
            'data_jp' => $data_jp,
            'data_dokter' => $data_dokter
        ]);
    }
    public function submitPercentage(Request $request)
{
    
    $request->validate([
        'percentage' => 'required|numeric|between:1,100',
    ], [
        'percentage.required' => 'Persentase harus diisi.',
        'percentage.numeric' => 'Persentase harus berupa angka.',
        'percentage.between' => 'Persentase harus berada dalam rentang 1 hingga 100.',
    ]);

    $userId = Auth::id();
    $percentage = PercentageJsJp::firstOrNew(['user_id' => $userId]);
    $percentage->jp = $request->percentage;
    $percentage->js = 100 - $request->percentage;
    $percentage->save();
    return response()->json(['data' => $request->percentage]);
}
    public function submitPercentageJlJtl(Request $request)
{
    $request->validate([
        'percentage' => 'required|numeric|between:1,100',
    ], [
        'percentage.required' => 'Persentase harus diisi.',
        'percentage.numeric' => 'Persentase harus berupa angka.',
        'percentage.between' => 'Persentase harus berada dalam rentang 1 hingga 100.',
    ]);
    
    $userId = Auth::id();
    // Mencari data persentase berdasarkan ID pengguna yang sedang login
    $percentage = PercentageJlJtl::firstOrNew(['user_id' => $userId]);
    $percentage->jl = $request->percentage;
    $percentage->jtl = 100 - $request->percentage;
    $percentage->save();

    return response()->json( [
        'jl' => $request->percentage,
        'jtl' => 100 - $request->percentage
    ]);
        
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
