<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Paket;
use App\Models\User;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pelanggan = Pelanggan::latest()->paginate(10);
        
        // Load user dan paket secara manual karena tidak ada foreign key
        foreach ($pelanggan as $p) {
            $p->user = $p->getUser();
            $p->paket = $p->getPaket();
        }
        
        return view('admin.pelanggan.index', compact('pelanggan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $paket = Paket::where('status', 'aktif')->get();
        return view('admin.pelanggan.create', compact('paket'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'paket_id' => 'required|exists:paket,paket_id',
            'alamat' => 'required|string',
            'no_telepon' => 'required|string|max:20',
            'tanggal_pemasangan' => 'required|date',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        DB::beginTransaction();
        try {
            // Buat user baru dengan generate user_id
            $user = User::create([
                'user_id' => User::generateUserId(),
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'pelanggan',
            ]);

            // Generate pelanggan_id dengan format baru: PLG001-USR1-PKT1
            $pelangganId = Pelanggan::generatePelangganId($user->user_id, $validated['paket_id']);

            // Buat data pelanggan
            $pelanggan = Pelanggan::create([
                'pelanggan_id' => $pelangganId,
                'alamat' => $validated['alamat'],
                'no_telepon' => $validated['no_telepon'],
                'tanggal_pemasangan' => $validated['tanggal_pemasangan'],
                'status' => $validated['status'],
            ]);

            // Buat tagihan pemasangan pertama
            $paket = Paket::find($validated['paket_id']); // paket_id sudah format PKT1, dst
            $totalTagihan = $paket->harga_pemasangan + $paket->harga_bulanan;
            
            Tagihan::create([
                'tagihan_id' => Tagihan::generateTagihanId(),
                'pelanggan_id' => $pelanggan->pelanggan_id,
                'paket_id' => $validated['paket_id'],
                'bulan' => Carbon::parse($validated['tanggal_pemasangan'])->month,
                'tahun' => Carbon::parse($validated['tanggal_pemasangan'])->year,
                'jumlah_tagihan' => $totalTagihan,
                'status' => 'belum_bayar',
                'tanggal_jatuh_tempo' => Carbon::parse($validated['tanggal_pemasangan'])->addDays(7),
                'jenis_tagihan' => 'pemasangan',
                'keterangan' => 'Tagihan pemasangan pertama',
            ]);

            DB::commit();

            return redirect()->route('admin.pelanggan.index')
                ->with('success', 'Pelanggan berhasil ditambahkan dan tagihan pemasangan telah dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menambahkan pelanggan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pelanggan $pelanggan)
    {
        // Load user dan paket secara manual
        $pelanggan->user = $pelanggan->getUser();
        $pelanggan->paket = $pelanggan->getPaket();
        $pelanggan->load(['tagihan', 'pembayaran']);
        return view('admin.pelanggan.show', compact('pelanggan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pelanggan $pelanggan)
    {
        $paket = Paket::where('status', 'aktif')->get();
        // Load user secara manual
        $pelanggan->user = $pelanggan->getUser();
        return view('admin.pelanggan.edit', compact('pelanggan', 'paket'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pelanggan $pelanggan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $pelanggan->user_id . ',user_id',
            'password' => 'nullable|string|min:8|confirmed',
            'paket_id' => 'required|exists:paket,paket_id',
            'alamat' => 'required|string',
            'no_telepon' => 'required|string|max:20',
            'tanggal_pemasangan' => 'required|date',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        DB::beginTransaction();
        try {
            // Get user
            $user = $pelanggan->getUser();
            
            // Update user
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];
            
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $user->update($userData);

            // Jika paket berubah, generate pelanggan_id baru dengan format baru
            $paketLama = $pelanggan->paket_id;
            if ($paketLama != $validated['paket_id']) {
                $pelangganIdBaru = Pelanggan::generatePelangganId($user->user_id, $validated['paket_id']);
                $pelanggan->update([
                    'pelanggan_id' => $pelangganIdBaru,
                    'alamat' => $validated['alamat'],
                    'no_telepon' => $validated['no_telepon'],
                    'tanggal_pemasangan' => $validated['tanggal_pemasangan'],
                    'status' => $validated['status'],
                ]);
            } else {
                // Update pelanggan tanpa mengubah pelanggan_id
                $pelanggan->update([
                    'alamat' => $validated['alamat'],
                    'no_telepon' => $validated['no_telepon'],
                    'tanggal_pemasangan' => $validated['tanggal_pemasangan'],
                    'status' => $validated['status'],
                ]);
            }

            DB::commit();

            return redirect()->route('admin.pelanggan.index')
                ->with('success', 'Pelanggan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui pelanggan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pelanggan $pelanggan)
    {
        DB::beginTransaction();
        try {
            // Hapus tagihan dan pembayaran terkait
            $pelanggan->tagihan()->delete();
            $pelanggan->pembayaran()->delete();
            $pelanggan->permintaanUpgrade()->delete();
            
            // Hapus user dan pelanggan
            $user = $pelanggan->getUser();
            $pelanggan->delete();
            if ($user) {
                $user->delete();
            }

            DB::commit();

            return redirect()->route('admin.pelanggan.index')
                ->with('success', 'Pelanggan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.pelanggan.index')
                ->with('error', 'Terjadi kesalahan saat menghapus pelanggan: ' . $e->getMessage());
        }
    }
}
