<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use Illuminate\Http\Request;

class LemburController extends Controller
{
    public function index(Request $request)
{
    // Get logged-in user ID
    $userId = auth()->user()->id;

    // Get bulan (month) and tahun (year) from the request
    $bulan = $request->input('bulan');
    $tahun = $request->input('tahun');

    // Start the query for lembur, filtering by the logged-in user's ID
    $lemburQuery = Lembur::where('id_karyawan', $userId);

    // If bulan and tahun are provided, filter by those as well
    if ($bulan && $tahun) {
        $lemburQuery->whereYear('tanggal_lembur', $tahun)
                    ->whereMonth('tanggal_lembur', $bulan);
    }

    // Get the lembur records
    $lembur = $lemburQuery->get();

    return response()->json($lembur);
}
}