<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presence; // Pastikan untuk mengimpor model Presence
use Carbon\Carbon;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Ambil riwayat kehadiran berdasarkan tanggal jika filter diisi
            if ($request->filled('startDate') && $request->filled('endDate')) {
                $startDate = $request->input('startDate');
                $endDate = $request->input('endDate');
                $presences = Presence::latest()
                    ->where('karyawan_id', auth()->guard('api')->id())
                    ->whereBetween('date', [$startDate, $endDate])
                    ->get();
            } else {
                // Jika filter tidak diisi, ambil riwayat kehadiran untuk bulan dan tahun saat ini
                $startOfMonth = Carbon::now()->startOfMonth();
                $endOfMonth = Carbon::now()->endOfMonth();
                $presences = Presence::latest()
                    ->where('karyawan_id', auth()->guard('api')->id())
                    ->whereBetween('date', [$startOfMonth, $endOfMonth])
                    ->get();
            }
    
            return response()->json([
                'success' => true, 
                'message' => 'Load data successfully',
                'presences' => $presences
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to load presence data: ' . $e->getMessage()
            ], 500);
        }
    }
    
}