<?php

namespace App\Http\Controllers;

use App\Models\KategoriPendapatan;
use Illuminate\Http\Request;

class KategoriPendapatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show()
    {
        $data = KategoriPendapatan::orderBy('kategori')->get();
        return response()->json(['data' => $data], 200);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KategoriPendapatan $kategoriPendapatan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KategoriPendapatan $kategoriPendapatan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KategoriPendapatan $kategoriPendapatan)
    {
        //
    }
}
