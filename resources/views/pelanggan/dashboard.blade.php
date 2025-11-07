<x-pelanggan-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Pelanggan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Informasi Pelanggan -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pelanggan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Nama</p>
                            <p class="font-medium text-gray-900">{{ $pelanggan->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="font-medium text-gray-900">{{ $pelanggan->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Paket Aktif</p>
                            <p class="font-medium text-gray-900">{{ $pelanggan->paket->nama_paket }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Kecepatan</p>
                            <p class="font-medium text-gray-900">{{ $pelanggan->paket->kecepatan }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Alamat</p>
                            <p class="font-medium text-gray-900">{{ $pelanggan->alamat }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">No. Telepon</p>
                            <p class="font-medium text-gray-900">{{ $pelanggan->no_telepon }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Tagihan Belum Bayar</div>
                        <div class="text-2xl font-bold text-gray-900 mt-2">{{ $totalTagihanBelumBayar }}</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Paket Aktif</div>
                        <div class="text-2xl font-bold text-blue-600 mt-2">{{ $pelanggan->paket->nama_paket }}</div>
                    </div>
                </div>
            </div>

            <!-- Tagihan Bulan Ini -->
            @if($tagihanBulanIni)
                <div class="bg-white border border-yellow-200 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Tagihan Bulan Ini</h3>
                                <p class="text-gray-700">Jumlah: <span class="font-bold">Rp {{ number_format($tagihanBulanIni->jumlah_tagihan, 0, ',', '.') }}</span></p>
                                <p class="text-sm text-gray-600">Jatuh Tempo: {{ $tagihanBulanIni->tanggal_jatuh_tempo->format('d M Y') }}</p>
                            </div>
                            <a href="{{ route('pelanggan.pembayaran.create', $tagihanBulanIni) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">
                                Bayar Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Riwayat Pembayaran Terbaru -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Pembayaran Terbaru</h3>
                    @if($pembayaranTerbaru->count() > 0)
                        <div class="overflow-x-auto w-full">
                            <table class="min-w-full w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($pembayaranTerbaru as $pembayaran)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $pembayaran->created_at->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($pembayaran->status === 'lunas')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Lunas</span>
                                                @elseif($pembayaran->status === 'menunggu_verifikasi')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Menunggu Verifikasi</span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('pelanggan.pembayaran.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Lihat Semua →
                            </a>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Belum ada riwayat pembayaran</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-pelanggan-layout>

