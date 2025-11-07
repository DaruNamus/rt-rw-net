<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\Pelanggan;
use App\Models\Paket;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TagihanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Tagihan::with(['pelanggan.user', 'paket']);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan jenis tagihan
        if ($request->filled('jenis_tagihan')) {
            $query->where('jenis_tagihan', $request->jenis_tagihan);
        }

        // Filter berdasarkan pelanggan
        if ($request->filled('pelanggan_id')) {
            $query->where('pelanggan_id', $request->pelanggan_id);
        }

        $tagihan = $query->latest()->paginate(15);
        $pelanggan = Pelanggan::where('status', 'aktif')->with('user')->get();

        return view('admin.tagihan.index', compact('tagihan', 'pelanggan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pelanggan = Pelanggan::where('status', 'aktif')->with(['user', 'paket'])->get();
        $paket = Paket::where('status', 'aktif')->get();
        
        return view('admin.tagihan.create', compact('pelanggan', 'paket'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'paket_id' => 'required|exists:paket,id',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2100',
            'jumlah_tagihan' => 'required|numeric|min:0',
            'tanggal_jatuh_tempo' => 'required|date',
            'jenis_tagihan' => 'required|in:bulanan,upgrade,pemasangan',
            'keterangan' => 'nullable|string',
        ]);

        // Cek apakah tagihan untuk bulan dan tahun yang sama sudah ada
        $existingTagihan = Tagihan::where('pelanggan_id', $validated['pelanggan_id'])
            ->where('bulan', $validated['bulan'])
            ->where('tahun', $validated['tahun'])
            ->where('jenis_tagihan', $validated['jenis_tagihan'])
            ->first();

        if ($existingTagihan) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tagihan untuk bulan dan tahun tersebut sudah ada.');
        }

        Tagihan::create($validated);

        return redirect()->route('admin.tagihan.index')
            ->with('success', 'Tagihan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tagihan $tagihan)
    {
        $tagihan->load(['pelanggan.user', 'paket', 'pembayaran']);
        return view('admin.tagihan.show', compact('tagihan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tagihan $tagihan)
    {
        $pelanggan = Pelanggan::where('status', 'aktif')->with(['user', 'paket'])->get();
        $paket = Paket::where('status', 'aktif')->get();
        $tagihan->load(['pelanggan', 'paket']);
        
        return view('admin.tagihan.edit', compact('tagihan', 'pelanggan', 'paket'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tagihan $tagihan)
    {
        $validated = $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'paket_id' => 'required|exists:paket,id',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2100',
            'jumlah_tagihan' => 'required|numeric|min:0',
            'tanggal_jatuh_tempo' => 'required|date',
            'jenis_tagihan' => 'required|in:bulanan,upgrade,pemasangan',
            'status' => 'required|in:belum_bayar,lunas',
            'keterangan' => 'nullable|string',
        ]);

        // Cek apakah tagihan untuk bulan dan tahun yang sama sudah ada (kecuali tagihan yang sedang diedit)
        $existingTagihan = Tagihan::where('pelanggan_id', $validated['pelanggan_id'])
            ->where('bulan', $validated['bulan'])
            ->where('tahun', $validated['tahun'])
            ->where('jenis_tagihan', $validated['jenis_tagihan'])
            ->where('id', '!=', $tagihan->id)
            ->first();

        if ($existingTagihan) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tagihan untuk bulan dan tahun tersebut sudah ada.');
        }

        $tagihan->update($validated);

        return redirect()->route('admin.tagihan.index')
            ->with('success', 'Tagihan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tagihan $tagihan)
    {
        // Cek apakah tagihan sudah ada pembayaran
        if ($tagihan->pembayaran()->count() > 0) {
            return redirect()->route('admin.tagihan.index')
                ->with('error', 'Tagihan tidak dapat dihapus karena sudah ada pembayaran terkait.');
        }

        $tagihan->delete();

        return redirect()->route('admin.tagihan.index')
            ->with('success', 'Tagihan berhasil dihapus.');
    }
}
