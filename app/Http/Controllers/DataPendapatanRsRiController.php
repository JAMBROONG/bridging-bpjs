<?php

namespace App\Http\Controllers;

use App\Models\DataPendapatanRsRi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataPendapatanRsRiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('dataPendapatanRsRi')->get();

        $dataPendapatans = DataPendapatanRsRi::with('user')->get();

        $result = [
            'users' => $users->toArray(),
            'data_pendapatans' => $dataPendapatans->toArray(),
        ];

        return $result;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    public function shifftingShow()
    {
        $user = Auth::user();
        $dataPendapatan = $user->dataPendapatan;

        $result = [
            'user' => $user,
            'data_pendapatan' => $dataPendapatan,
        ];

        return $result;
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
