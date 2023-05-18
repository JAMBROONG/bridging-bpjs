<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Invoices;
use App\Models\Subscribe;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class App extends Controller
{
    public function index()
    {
        $userId = Auth::id(); // Mendapatkan ID user yang sedang login
        return Inertia::render('Dashboard', []);
    }
    public function uploadShifting(Request $request)
    {
        $request->validate([
            'file1' => 'required|file|mimes:xlsx,xls',
            'file2' => 'required|file|mimes:xlsx,xls',
        ]);
        $file1 = $request->file('file1');
        $file2 = $request->file('file2');
        $path1 = $file1->store('excels');
        $path2 = $file2->store('excels');
        $rows1 = Excel::toArray([], $path1);
        $data1 = [];
        foreach ($rows1[0] as $row) {
            $data1[] = $row;
        }

        // Membaca file kedua
        $rows2 = Excel::toArray([], $path2);
        $data2 = [];
        foreach ($rows2[0] as $row) {
            $data2[] = $row;
        }

        // Menggabungkan data dari file pertama dan kedua
        $mergedData = array_merge($data1, $data2);

        $response = [
            'data' => $mergedData,
        ];

        return response()->json($response);
    }
}
