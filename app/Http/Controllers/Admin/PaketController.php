<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paket;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paket = Paket::latest()->paginate(10);
        return view('admin.paket.index', compact('paket'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.paket.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_paket' => 'required|string|max:255',
            'harga_bulanan' => 'required|numeric|min:0',
            'harga_pemasangan' => 'nullable|numeric|min:0',
            'kecepatan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,tidak_aktif',
        ]);

        Paket::create($validated);

        return redirect()->route('admin.paket.index')
            ->with('success', 'Paket berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Paket $paket)
    {
        return view('admin.paket.show', compact('paket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Paket $paket)
    {
        return view('admin.paket.edit', compact('paket'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Paket $paket)
    {
        $validated = $request->validate([
            'nama_paket' => 'required|string|max:255',
            'harga_bulanan' => 'required|numeric|min:0',
            'harga_pemasangan' => 'nullable|numeric|min:0',
            'kecepatan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,tidak_aktif',
        ]);

        $paket->update($validated);

        return redirect()->route('admin.paket.index')
            ->with('success', 'Paket berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paket $paket)
    {
        // Cek apakah paket sedang digunakan
        if ($paket->pelanggan()->count() > 0) {
            return redirect()->route('admin.paket.index')
                ->with('error', 'Paket tidak dapat dihapus karena sedang digunakan oleh pelanggan.');
        }

        $paket->delete();

        return redirect()->route('admin.paket.index')
            ->with('success', 'Paket berhasil dihapus.');
    }
}
