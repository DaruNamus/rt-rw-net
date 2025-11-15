<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Paket;
use App\Models\PermintaanUpgrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermintaanUpgradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $pelanggan = Pelanggan::findByUserId($user->id);

        if (!$pelanggan) {
            return redirect()->route('pelanggan.dashboard')
                ->with('error', 'Data pelanggan tidak ditemukan.');
        }

        $permintaanUpgrade = PermintaanUpgrade::where('pelanggan_id', $pelanggan->pelanggan_id)
            ->with(['paketLama', 'paketBaru', 'diprosesOleh'])
            ->latest()
            ->paginate(15);

        return view('pelanggan.permintaan-upgrade.index', compact('permintaanUpgrade', 'pelanggan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $pelanggan = Pelanggan::findByUserId($user->id);

        if (!$pelanggan) {
            return redirect()->route('pelanggan.dashboard')
                ->with('error', 'Data pelanggan tidak ditemukan.');
        }

        // Ambil paket yang tersedia (kecuali paket yang sedang digunakan)
        $paket = Paket::where('status', 'aktif')
            ->where('id', '!=', $pelanggan->paket_id)
            ->get();

        // Cek apakah ada permintaan upgrade yang masih menunggu
        $permintaanMenunggu = PermintaanUpgrade::where('pelanggan_id', $pelanggan->pelanggan_id)
            ->where('status', 'menunggu')
            ->exists();

        return view('pelanggan.permintaan-upgrade.create', compact('pelanggan', 'paket', 'permintaanMenunggu'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $pelanggan = Pelanggan::findByUserId($user->id);

        if (!$pelanggan) {
            return redirect()->route('pelanggan.dashboard')
                ->with('error', 'Data pelanggan tidak ditemukan.');
        }

        $validated = $request->validate([
            'paket_baru_id' => 'required|exists:paket,id|different:' . $pelanggan->paket_id,
            'alasan' => 'nullable|string|max:500',
        ], [
            'paket_baru_id.different' => 'Paket baru harus berbeda dengan paket yang sedang digunakan.',
        ]);

        // Cek apakah ada permintaan upgrade yang masih menunggu
        $permintaanMenunggu = PermintaanUpgrade::where('pelanggan_id', $pelanggan->pelanggan_id)
            ->where('status', 'menunggu')
            ->exists();

        if ($permintaanMenunggu) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Anda masih memiliki permintaan upgrade yang menunggu persetujuan.');
        }

        // Buat permintaan upgrade
        PermintaanUpgrade::create([
            'pelanggan_id' => $pelanggan->pelanggan_id,
            'paket_lama_id' => $pelanggan->paket_id,
            'paket_baru_id' => $validated['paket_baru_id'],
            'status' => 'menunggu',
            'alasan' => $validated['alasan'],
        ]);

        return redirect()->route('pelanggan.permintaan-upgrade.index')
            ->with('success', 'Permintaan upgrade berhasil dikirim. Menunggu persetujuan admin.');
    }
}
