<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pembayaran::with(['pelanggan.user', 'tagihan.paket', 'diverifikasiOleh']);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default: tampilkan yang menunggu verifikasi
            $query->where('status', 'menunggu_verifikasi');
        }

        $pembayaran = $query->latest()->paginate(15);

        return view('admin.pembayaran.index', compact('pembayaran'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Pembayaran $pembayaran)
    {
        $pembayaran->load(['pelanggan.user', 'tagihan.paket', 'diverifikasiOleh']);
        return view('admin.pembayaran.show', compact('pembayaran'));
    }

    /**
     * Verifikasi pembayaran
     */
    public function verifikasi(Request $request, Pembayaran $pembayaran)
    {
        $request->validate([
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Update status pembayaran
            $pembayaran->update([
                'status' => 'lunas',
                'diverifikasi_oleh' => Auth::id(),
                'diverifikasi_pada' => now(),
                'catatan_admin' => $request->catatan_admin,
            ]);

            // Update status tagihan menjadi lunas
            $tagihan = $pembayaran->tagihan;
            $tagihan->update([
                'status' => 'lunas',
            ]);

            DB::commit();

            return redirect()->route('admin.pembayaran.index')
                ->with('success', 'Pembayaran berhasil diverifikasi dan tagihan telah ditandai sebagai lunas.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memverifikasi pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Tolak pembayaran
     */
    public function tolak(Request $request, Pembayaran $pembayaran)
    {
        $request->validate([
            'catatan_admin' => 'required|string|max:500',
        ], [
            'catatan_admin.required' => 'Catatan admin wajib diisi saat menolak pembayaran.',
        ]);

        DB::beginTransaction();
        try {
            // Update status pembayaran
            $pembayaran->update([
                'status' => 'ditolak',
                'diverifikasi_oleh' => Auth::id(),
                'diverifikasi_pada' => now(),
                'catatan_admin' => $request->catatan_admin,
            ]);

            DB::commit();

            return redirect()->route('admin.pembayaran.index')
                ->with('success', 'Pembayaran telah ditolak.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menolak pembayaran: ' . $e->getMessage());
        }
    }
}
