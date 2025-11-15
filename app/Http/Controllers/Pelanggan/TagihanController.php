<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagihanController extends Controller
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

        $tagihan = Tagihan::where('pelanggan_id', $pelanggan->pelanggan_id)
            ->with(['paket'])
            ->latest()
            ->paginate(15);

        return view('pelanggan.tagihan.index', compact('tagihan', 'pelanggan'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Tagihan $tagihan)
    {
        $user = Auth::user();
        $pelanggan = Pelanggan::findByUserId($user->id);

        if (!$pelanggan || $tagihan->pelanggan_id !== $pelanggan->pelanggan_id) {
            return redirect()->route('pelanggan.tagihan.index')
                ->with('error', 'Tagihan tidak ditemukan.');
        }

        $tagihan->load(['paket', 'pembayaran']);
        
        return view('pelanggan.tagihan.show', compact('tagihan', 'pelanggan'));
    }
}
