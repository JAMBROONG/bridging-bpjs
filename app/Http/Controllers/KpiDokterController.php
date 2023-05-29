<?php

namespace App\Http\Controllers;

use App\Models\DataDokter;
use App\Models\KpiDokter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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
        return Inertia::render('KpiDokter', [
            'data_dokter' => $data_dokter,
            'kpi_dokter' => $kpi_dokter
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
    public function update(Request $request, KpiDokter $kpiDokter)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KpiDokter $kpiDokter)
    {
        //
    }
}
