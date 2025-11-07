<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pelanggan = Pelanggan::where('user_id', $user->id)->first();

        if (!$pelanggan) {
            return redirect()->route('profile.edit')->with('error', 'Data pelanggan tidak ditemukan. Silakan hubungi admin untuk melengkapi data pelanggan Anda.');
        }

        $tagihanBulanIni = Tagihan::where('pelanggan_id', $pelanggan->id)
            ->where('status', 'belum_bayar')
            ->whereMonth('tanggal_jatuh_tempo', now()->month)
            ->whereYear('tanggal_jatuh_tempo', now()->year)
            ->first();

        $totalTagihanBelumBayar = Tagihan::where('pelanggan_id', $pelanggan->id)
            ->where('status', 'belum_bayar')
            ->count();

        $pembayaranTerbaru = Pembayaran::where('pelanggan_id', $pelanggan->id)
            ->with('tagihan')
            ->latest()
            ->take(5)
            ->get();

        return view('pelanggan.dashboard', compact(
            'pelanggan',
            'tagihanBulanIni',
            'totalTagihanBelumBayar',
            'pembayaranTerbaru'
        ));
    }
}
