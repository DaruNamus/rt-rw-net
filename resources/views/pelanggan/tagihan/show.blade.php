<x-pelanggan-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Tagihan
            </h2>
            <a href="{{ route('pelanggan.tagihan.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-md transition ease-in-out duration-150">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Informasi Tagihan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Tagihan</h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500">Paket</p>
                                <p class="font-medium text-gray-900">{{ $tagihan->paket->nama_paket }} - {{ $tagihan->paket->kecepatan }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Periode</p>
                                <p class="font-medium text-gray-900">
                                    {{ \Carbon\Carbon::create()->month($tagihan->bulan)->locale('id')->translatedFormat('F') }} {{ $tagihan->tahun }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Jumlah Tagihan</p>
                                <p class="font-medium text-gray-900 text-lg">Rp {{ number_format($tagihan->jumlah_tagihan, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Tanggal Jatuh Tempo</p>
                                <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Jenis Tagihan</p>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded bg-blue-100 text-blue-800">
                                    {{ ucfirst($tagihan->jenis_tagihan) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Status</p>
                                @if($tagihan->status === 'lunas')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Lunas
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Belum Bayar
                                    </span>
                                @endif
                            </div>
                            @if($tagihan->keterangan)
                                <div>
                                    <p class="text-sm text-gray-500">Keterangan</p>
                                    <p class="font-medium text-gray-900">{{ $tagihan->keterangan }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Riwayat Pembayaran & Aksi -->
                <div class="space-y-6">
                    <!-- Riwayat Pembayaran -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Pembayaran</h3>
                            @if($tagihan->pembayaran->count() > 0)
                                <div class="space-y-3">
                                    @foreach($tagihan->pembayaran as $pembayaran)
                                        <div class="border-b border-gray-200 pb-3 last:border-b-0">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <p class="font-medium text-gray-900">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</p>
                                                    <p class="text-sm text-gray-500">{{ $pembayaran->created_at->format('d M Y H:i') }}</p>
                                                    @if($pembayaran->catatan_admin)
                                                        <p class="text-xs text-gray-400 mt-1">{{ $pembayaran->catatan_admin }}</p>
                                                    @endif
                                                </div>
                                                <div>
                                                    @if($pembayaran->status === 'lunas')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            Lunas
                                                        </span>
                                                    @elseif($pembayaran->status === 'menunggu_verifikasi')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                            Menunggu
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                            Ditolak
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-sm">Belum ada pembayaran untuk tagihan ini</p>
                            @endif
                        </div>
                    </div>

                    <!-- Tombol Bayar -->
                    @if($tagihan->status === 'belum_bayar')
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <a href="{{ route('pelanggan.pembayaran.create', $tagihan) }}" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-md transition ease-in-out duration-150 text-center block">
                                    Upload Bukti Pembayaran
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-pelanggan-layout>

