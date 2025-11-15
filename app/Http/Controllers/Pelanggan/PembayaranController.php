<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
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

        $pembayaran = Pembayaran::where('pelanggan_id', $pelanggan->pelanggan_id)
            ->with(['tagihan.paket'])
            ->latest()
            ->paginate(15);

        return view('pelanggan.pembayaran.index', compact('pembayaran', 'pelanggan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Tagihan $tagihan)
    {
        $user = Auth::user();
        $pelanggan = Pelanggan::findByUserId($user->id);

        if (!$pelanggan || $tagihan->pelanggan_id !== $pelanggan->pelanggan_id) {
            return redirect()->route('pelanggan.tagihan.index')
                ->with('error', 'Tagihan tidak ditemukan.');
        }

        // Cek apakah tagihan sudah lunas
        if ($tagihan->status === 'lunas') {
            return redirect()->route('pelanggan.tagihan.show', $tagihan)
                ->with('error', 'Tagihan ini sudah lunas.');
        }

        $tagihan->load('paket');
        
        return view('pelanggan.pembayaran.create', compact('tagihan', 'pelanggan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Tagihan $tagihan)
    {
        $user = Auth::user();
        $pelanggan = Pelanggan::findByUserId($user->id);

        if (!$pelanggan || $tagihan->pelanggan_id !== $pelanggan->pelanggan_id) {
            return redirect()->route('pelanggan.tagihan.index')
                ->with('error', 'Tagihan tidak ditemukan.');
        }

        $validated = $request->validate([
            'jumlah_bayar' => 'required|numeric|min:0',
            'tanggal_bayar' => 'required|date',
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Upload bukti pembayaran
        if ($request->hasFile('bukti_pembayaran')) {
            $buktiPembayaran = $request->file('bukti_pembayaran');
            $path = $buktiPembayaran->store('bukti_pembayaran', 'public');
            $validated['bukti_pembayaran'] = $path;
        }

        // Buat pembayaran
        Pembayaran::create([
            'tagihan_id' => $tagihan->id,
            'pelanggan_id' => $pelanggan->pelanggan_id,
            'jumlah_bayar' => $validated['jumlah_bayar'],
            'tanggal_bayar' => $validated['tanggal_bayar'],
            'bukti_pembayaran' => $validated['bukti_pembayaran'],
            'status' => 'menunggu_verifikasi',
        ]);

        return redirect()->route('pelanggan.pembayaran.index')
            ->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');
    }
}
