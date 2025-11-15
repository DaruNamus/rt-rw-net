<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Admin
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistik Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Total Paket Aktif</div>
                        <div class="text-2xl font-bold text-gray-900 mt-2">{{ $totalPaket }}</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Total Pelanggan Aktif</div>
                        <div class="text-2xl font-bold text-gray-900 mt-2">{{ $totalPelanggan }}</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Tagihan Belum Bayar</div>
                        <div class="text-2xl font-bold text-gray-900 mt-2">{{ $totalTagihan }}</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Pembayaran Menunggu</div>
                        <div class="text-2xl font-bold text-yellow-600 mt-2">{{ $totalPembayaranMenunggu }}</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Permintaan Upgrade</div>
                        <div class="text-2xl font-bold text-blue-600 mt-2">{{ $totalPermintaanUpgrade }}</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Pembayaran Terbaru -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pembayaran Menunggu Verifikasi</h3>
                        @if($pembayaranTerbaru->count() > 0)
                            <div class="space-y-4">
                                @foreach($pembayaranTerbaru as $pembayaran)
                                    <div class="border-b border-gray-200 pb-4 last:border-b-0 last:pb-0">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $pembayaran->pelanggan && $pembayaran->pelanggan->user ? $pembayaran->pelanggan->user->name : 'N/A' }}</p>
                                                <p class="text-sm text-gray-500">Tagihan: {{ $pembayaran->tagihan ? $pembayaran->tagihan->jenis_tagihan : 'N/A' }}</p>
                                                <p class="text-sm text-gray-500">Jumlah: Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</p>
                                            </div>
                                            <a href="{{ route('admin.pembayaran.show', $pembayaran) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.pembayaran.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Lihat Semua →
                                </a>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Tidak ada pembayaran menunggu verifikasi</p>
                        @endif
                    </div>
                </div>

                <!-- Permintaan Upgrade Terbaru -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Permintaan Upgrade Menunggu</h3>
                        @if($permintaanUpgradeTerbaru->count() > 0)
                            <div class="space-y-4">
                                @foreach($permintaanUpgradeTerbaru as $permintaan)
                                    <div class="border-b border-gray-200 pb-4 last:border-b-0 last:pb-0">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $permintaan->pelanggan && $permintaan->pelanggan->user ? $permintaan->pelanggan->user->name : 'N/A' }}</p>
                                                <p class="text-sm text-gray-500">{{ $permintaan->paketLama ? $permintaan->paketLama->nama_paket : 'N/A' }} → {{ $permintaan->paketBaru ? $permintaan->paketBaru->nama_paket : 'N/A' }}</p>
                                                <p class="text-xs text-gray-400">{{ $permintaan->created_at->format('d M Y') }}</p>
                                            </div>
                                            <a href="{{ route('admin.permintaan-upgrade.show', $permintaan) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.permintaan-upgrade.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Lihat Semua →
                                </a>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Tidak ada permintaan upgrade menunggu</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

