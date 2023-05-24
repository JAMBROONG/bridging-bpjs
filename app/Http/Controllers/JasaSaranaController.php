<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\JasaSarana;
use App\Models\PercentageJsJp;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JasaSaranaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id();
        $data = PercentageJsJp::where('user_id', $userId)->get();
        return Inertia::render('JasaSarana', [
            'data' => $data
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
    public function show(JasaSarana $jasaSarana)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JasaSarana $jasaSarana)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JasaSarana $jasaSarana)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JasaSarana $jasaSarana)
    {
        //
    }
}
