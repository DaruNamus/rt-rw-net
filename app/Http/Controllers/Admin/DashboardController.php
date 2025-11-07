<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\PermintaanUpgrade;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPaket = Paket::where('status', 'aktif')->count();
        $totalPelanggan = Pelanggan::where('status', 'aktif')->count();
        $totalTagihan = Tagihan::where('status', 'belum_bayar')->count();
        $totalPembayaranMenunggu = Pembayaran::where('status', 'menunggu_verifikasi')->count();
        $totalPermintaanUpgrade = PermintaanUpgrade::where('status', 'menunggu')->count();

        $pembayaranTerbaru = Pembayaran::with(['pelanggan.user', 'tagihan'])
            ->where('status', 'menunggu_verifikasi')
            ->latest()
            ->take(5)
            ->get();

        $permintaanUpgradeTerbaru = PermintaanUpgrade::with(['pelanggan.user', 'paketLama', 'paketBaru'])
            ->where('status', 'menunggu')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalPaket',
            'totalPelanggan',
            'totalTagihan',
            'totalPembayaranMenunggu',
            'totalPermintaanUpgrade',
            'pembayaranTerbaru',
            'permintaanUpgradeTerbaru'
        ));
    }
}
