<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Pembayaran
            </h2>
            <a href="{{ route('admin.pembayaran.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-md transition ease-in-out duration-150">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Informasi Pembayaran -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pembayaran</h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500">Pelanggan</p>
                                <p class="font-medium text-gray-900">{{ $pembayaran->pelanggan->user->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-medium text-gray-900">{{ $pembayaran->pelanggan->user->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Tagihan</p>
                                <p class="font-medium text-gray-900">
                                    {{ ucfirst($pembayaran->tagihan->jenis_tagihan) }} - 
                                    {{ \Carbon\Carbon::create()->month($pembayaran->tagihan->bulan)->locale('id')->translatedFormat('F') }} {{ $pembayaran->tagihan->tahun }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Paket</p>
                                <p class="font-medium text-gray-900">{{ $pembayaran->tagihan->paket->nama_paket }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Jumlah Tagihan</p>
                                <p class="font-medium text-gray-900">Rp {{ number_format($pembayaran->tagihan->jumlah_tagihan, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Jumlah Bayar</p>
                                <p class="font-medium text-gray-900 text-lg">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Tanggal Bayar</p>
                                <p class="font-medium text-gray-900">{{ $pembayaran->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Status</p>
                                @if($pembayaran->status === 'lunas')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Lunas
                                    </span>
                                @elseif($pembayaran->status === 'menunggu_verifikasi')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Menunggu Verifikasi
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Ditolak
                                    </span>
                                @endif
                            </div>
                            @if($pembayaran->diverifikasiOleh)
                                <div>
                                    <p class="text-sm text-gray-500">Diverifikasi Oleh</p>
                                    <p class="font-medium text-gray-900">{{ $pembayaran->diverifikasiOleh->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $pembayaran->diverifikasi_pada->format('d M Y H:i') }}</p>
                                </div>
                            @endif
                            @if($pembayaran->catatan_admin)
                                <div>
                                    <p class="text-sm text-gray-500">Catatan Admin</p>
                                    <p class="font-medium text-gray-900">{{ $pembayaran->catatan_admin }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Bukti Pembayaran & Aksi -->
                <div class="space-y-6">
                    <!-- Bukti Pembayaran -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Bukti Pembayaran</h3>
                            @if($pembayaran->bukti_pembayaran)
                                <div class="mb-4">
                                    <img src="{{ asset('storage/' . $pembayaran->bukti_pembayaran) }}" alt="Bukti Pembayaran" class="max-w-full h-auto rounded-lg border border-gray-200 shadow-sm">
                                </div>
                                <a href="{{ asset('storage/' . $pembayaran->bukti_pembayaran) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Lihat Full Size →
                                </a>
                            @else
                                <p class="text-gray-500 text-sm">Tidak ada bukti pembayaran</p>
                            @endif
                        </div>
                    </div>

                    <!-- Form Verifikasi/Tolak -->
                    @if($pembayaran->status === 'menunggu_verifikasi')
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Verifikasi</h3>
                                
                                <!-- Form Verifikasi -->
                                <form action="{{ route('admin.pembayaran.verifikasi', $pembayaran) }}" method="POST" class="mb-4">
                                    @csrf
                                    <div class="mb-4">
                                        <x-input-label for="catatan_verifikasi" :value="__('Catatan (Opsional)')" />
                                        <textarea id="catatan_verifikasi" name="catatan_admin" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Catatan untuk pelanggan (opsional)">{{ old('catatan_admin') }}</textarea>
                                    </div>
                                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-black font-medium py-2.5 px-4 rounded-md transition ease-in-out duration-150">
                                        Verifikasi Pembayaran
                                    </button>
                                </form>

                                <!-- Form Tolak -->
                                <form action="{{ route('admin.pembayaran.tolak', $pembayaran) }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <x-input-label for="catatan_tolak" :value="__('Alasan Penolakan')" />
                                        <textarea id="catatan_tolak" name="catatan_admin" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Wajib diisi saat menolak pembayaran" required>{{ old('catatan_admin') }}</textarea>
                                        <x-input-error :messages="$errors->get('catatan_admin')" class="mt-2" />
                                    </div>
                                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2.5 px-4 rounded-md transition ease-in-out duration-150" onclick="return confirm('Apakah Anda yakin ingin menolak pembayaran ini?');">
                                        Tolak Pembayaran
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

