<?php

namespace App\Http\Controllers;

use App\Models\VendorObat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class VendorObatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = VendorObat::where('user_id',Auth::id())->get();
        
        return Inertia::render('VendorObat', [
            'data' => $data
        ]);
    }

    public function addVendor(Request $request)
    {
        $vendor = new VendorObat();
        $vendor->vendor = $request->input('vendor');
        $vendor->user_id = Auth::id();
        $vendor->save();
        $data = VendorObat::where('user_id',Auth::id())->get();
        return response()->json([
            'data' => $data
        ]);
    }

    public function updateVendor(Request $request)
    {
        $vendor = VendorObat::find($request->input('id'));
        $vendor->vendor = $request->input('vendor');
        $vendor->save();

        return response()->json($vendor);
    }

    public function deleteVendor(Request $request)
    {
        $vendor = VendorObat::find($request->input('id'));
        $vendor->delete();

        // Tambahkan logika lainnya jika diperlukan

        return redirect()->back()->with('message', 'Vendor berhasil dihapus');
    }
}
