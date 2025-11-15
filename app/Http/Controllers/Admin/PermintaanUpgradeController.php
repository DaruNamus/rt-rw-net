<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermintaanUpgrade;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermintaanUpgradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PermintaanUpgrade::with(['pelanggan', 'paketLama', 'paketBaru', 'diprosesOleh']);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default: tampilkan yang menunggu
            $query->where('status', 'menunggu');
        }

        $permintaanUpgrade = $query->latest()->paginate(15);
        
        // Load user untuk setiap pelanggan secara manual
        foreach ($permintaanUpgrade as $pu) {
            if ($pu->pelanggan) {
                $pu->pelanggan->user = $pu->pelanggan->getUser();
            }
        }

        return view('admin.permintaan-upgrade.index', compact('permintaanUpgrade'));
    }

    /**
     * Display the specified resource.
     */
    public function show(PermintaanUpgrade $permintaanUpgrade)
    {
        $permintaanUpgrade->load(['pelanggan', 'paketLama', 'paketBaru', 'diprosesOleh']);
        if ($permintaanUpgrade->pelanggan) {
            $permintaanUpgrade->pelanggan->user = $permintaanUpgrade->pelanggan->getUser();
        }
        return view('admin.permintaan-upgrade.show', compact('permintaanUpgrade'));
    }

    /**
     * Setujui permintaan upgrade
     */
    public function setujui(Request $request, PermintaanUpgrade $permintaanUpgrade)
    {
        $request->validate([
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Update paket pelanggan dengan generate pelanggan_id baru
            $pelanggan = $permintaanUpgrade->pelanggan;
            $user = $pelanggan->getUser();
            
            // Generate pelanggan_id baru karena paket berubah
            $pelangganIdBaru = Pelanggan::generatePelangganId($user->id, $permintaanUpgrade->paket_baru_id);
            
            $pelanggan->update([
                'pelanggan_id' => $pelangganIdBaru,
            ]);

            // Buat tagihan upgrade
            $paketBaru = $permintaanUpgrade->paketBaru;
            $selisihHarga = $paketBaru->harga_bulanan - $permintaanUpgrade->paketLama->harga_bulanan;
            
            // Tagihan upgrade = selisih harga bulanan (untuk bulan ini)
            $bulanSekarang = now()->month;
            $tahunSekarang = now()->year;
            
            Tagihan::create([
                'pelanggan_id' => $pelanggan->pelanggan_id,
                'paket_id' => $permintaanUpgrade->paket_baru_id,
                'bulan' => $bulanSekarang,
                'tahun' => $tahunSekarang,
                'jumlah_tagihan' => max(0, $selisihHarga), // Jika downgrade, tetap 0
                'status' => 'belum_bayar',
                'tanggal_jatuh_tempo' => now()->addDays(7),
                'jenis_tagihan' => 'upgrade',
                'keterangan' => 'Tagihan upgrade dari ' . $permintaanUpgrade->paketLama->nama_paket . ' ke ' . $paketBaru->nama_paket,
            ]);

            // Update status permintaan upgrade
            $permintaanUpgrade->update([
                'status' => 'disetujui',
                'diproses_oleh' => Auth::id(),
                'diproses_pada' => now(),
                'catatan_admin' => $request->catatan_admin,
            ]);

            DB::commit();

            return redirect()->route('admin.permintaan-upgrade.index')
                ->with('success', 'Permintaan upgrade berhasil disetujui dan tagihan upgrade telah dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyetujui permintaan upgrade: ' . $e->getMessage());
        }
    }

    /**
     * Tolak permintaan upgrade
     */
    public function tolak(Request $request, PermintaanUpgrade $permintaanUpgrade)
    {
        $request->validate([
            'catatan_admin' => 'required|string|max:500',
        ], [
            'catatan_admin.required' => 'Catatan admin wajib diisi saat menolak permintaan upgrade.',
        ]);

        DB::beginTransaction();
        try {
            // Update status permintaan upgrade
            $permintaanUpgrade->update([
                'status' => 'ditolak',
                'diproses_oleh' => Auth::id(),
                'diproses_pada' => now(),
                'catatan_admin' => $request->catatan_admin,
            ]);

            DB::commit();

            return redirect()->route('admin.permintaan-upgrade.index')
                ->with('success', 'Permintaan upgrade telah ditolak.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menolak permintaan upgrade: ' . $e->getMessage());
        }
    }
}
