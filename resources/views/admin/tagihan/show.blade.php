@php
    function formatPhoneNumber($number) {
        $number = preg_replace('/[^0-9]/', '', $number); // Hapus non-numeric
        if (substr($number, 0, 1) === '0') {
            $number = '62' . substr($number, 1);
        }
        if (substr($number, 0, 2) !== '62') {
            $number = '62' . $number; // Default ke 62 jika format tidak dikenal
        }
        return $number;
    }
@endphp

<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Tagihan
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.tagihan.edit', $tagihan) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">
                    Edit
                </a>
                <a href="{{ route('admin.tagihan.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md">
                    Kembali
                </a>
            </div>
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
                                <p class="text-sm text-gray-500">Pelanggan</p>
                                <p class="font-medium text-gray-900">{{ $tagihan->pelanggan->user->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-medium text-gray-900">{{ $tagihan->pelanggan->user->email }}</p>
                            </div>
                             <div>
                                <p class="text-sm text-gray-500">No. Telepon</p>
                                <p class="font-medium text-gray-900">{{ $tagihan->pelanggan->no_telepon ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Paket</p>
                                <p class="font-medium text-gray-900">{{ $tagihan->paket->nama_paket }} - {{ $tagihan->paket->kecepatan }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Periode</p>
                                <p class="font-medium text-gray-900">{{ \Carbon\Carbon::create()->month($tagihan->bulan)->locale('id')->translatedFormat('F') }} {{ $tagihan->tahun }}</p>
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
                                    <div class="flex items-center space-x-2">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Belum Bayar
                                        </span>
                                        @if($tagihan->pelanggan->no_telepon)
                                            @php
                                                $waMessageRemind = "Halo {$tagihan->pelanggan->user->name},\n\n" .
                                                            "Mengingatkan kembali tagihan internet bulan " . \Carbon\Carbon::create()->month($tagihan->bulan)->locale('id')->translatedFormat('F') . " {$tagihan->tahun} belum terbayar.\n\n" .
                                                            "Total Tagihan: Rp " . number_format($tagihan->jumlah_tagihan, 0, ',', '.') . "\n" .
                                                            "Jatuh Tempo: " . \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->format('d M Y') . "\n\n" .
                                                            "Mohon segera melakukan pembayaran agar layanan tetap aktif.\n" .
                                                            "Abaikan jika sudah membayar.\n~ RT-RW Net";
                                                $waLinkRemind = "https://wa.me/" . formatPhoneNumber($tagihan->pelanggan->no_telepon) . "?text=" . urlencode($waMessageRemind);
                                            @endphp
                                            <a href="{{ $waLinkRemind }}" target="_blank" class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-400" title="Kirim Pengingat WhatsApp">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                </svg>
                                                <span class="ml-1">Ingatkan</span>
                                            </a>
                                        @endif
                                    </div>
                                @endif
                                
                                @if($tagihan->status === 'lunas' && $tagihan->jumlah_tagihan == 0)
                                    <div class="mt-2 flex space-x-2">
                                        <a href="{{ route('admin.tagihan.cetak', $tagihan) }}" target="_blank" class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" title="Cetak Bukti">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                            <span class="ml-1">Cetak</span>
                                        </a>

                                        @if($tagihan->pelanggan->no_telepon)
                                            @php
                                                $waMessage = "Halo {$tagihan->pelanggan->user->name},\n\n" .
                                                            "Informasi Tagihan Internet Anda:\n" .
                                                            "Periode: " . \Carbon\Carbon::create()->month($tagihan->bulan)->locale('id')->translatedFormat('F') . " {$tagihan->tahun}\n" .
                                                            "Total: Rp 0 (Lunas/Gratis)\n" .
                                                            "Status: LUNAS\n\n" .
                                                            "Terima kasih telah menggunakan layanan kami.\n~ RT-RW Net";
                                                $waLink = "https://wa.me/" . formatPhoneNumber($tagihan->pelanggan->no_telepon) . "?text=" . urlencode($waMessage);
                                            @endphp
                                            <a href="{{ $waLink }}" target="_blank" class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" title="Kirim WhatsApp">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                </svg>
                                                <span class="ml-1">WA</span>
                                            </a>
                                        @endif
                                    </div>
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
                                                    <div class="flex items-center space-x-1">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            Lunas
                                                        </span>
                                                        <a href="{{ route('admin.pembayaran.cetak', $pembayaran) }}" target="_blank" class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" title="Cetak Struk">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                            </svg>
                                                        </a>
                                                        @if($tagihan->pelanggan->no_telepon)
                                                            @php
                                                                $waMessagePay = "Halo {$tagihan->pelanggan->user->name},\n\n" .
                                                                            "Pembayaran Tagihan Internet:\n" .
                                                                            "Periode: " . \Carbon\Carbon::create()->month($tagihan->bulan)->locale('id')->translatedFormat('F') . " {$tagihan->tahun}\n" .
                                                                            "Jumlah: Rp " . number_format($pembayaran->jumlah_bayar, 0, ',', '.') . "\n" .
                                                                            "Status: LUNAS\n\n" .
                                                                            "Terima kasih.\n~ RT-RW Net";
                                                                $waLinkPay = "https://wa.me/" . formatPhoneNumber($tagihan->pelanggan->no_telepon) . "?text=" . urlencode($waMessagePay);
                                                            @endphp
                                                            <a href="{{ $waLinkPay }}" target="_blank" class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" title="Kirim WhatsApp">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                                </svg>
                                                            </a>
                                                        @endif
                                                    </div>
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
            </div>
        </div>
    </div>
</x-admin-layout>

