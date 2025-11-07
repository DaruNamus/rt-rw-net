<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Pelanggan
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.pelanggan.edit', $pelanggan) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">
                    Edit
                </a>
                <a href="{{ route('admin.pelanggan.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Informasi Pelanggan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pelanggan</h3>
                        <div class="space-y-4">
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
                                <p class="font-medium text-gray-900">{{ $pelanggan->paket->nama_paket }} - {{ $pelanggan->paket->kecepatan }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Alamat</p>
                                <p class="font-medium text-gray-900">{{ $pelanggan->alamat }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">No. Telepon</p>
                                <p class="font-medium text-gray-900">{{ $pelanggan->no_telepon }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Tanggal Pemasangan</p>
                                <p class="font-medium text-gray-900">{{ $pelanggan->tanggal_pemasangan->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Status</p>
                                @if($pelanggan->status === 'aktif')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Nonaktif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Riwayat Tagihan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Tagihan</h3>
                        @if($pelanggan->tagihan->count() > 0)
                            <div class="space-y-3">
                                @foreach($pelanggan->tagihan->take(5) as $tagihan)
                                    <div class="border-b border-gray-200 pb-3 last:border-b-0">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $tagihan->jenis_tagihan }}</p>
                                                <p class="text-sm text-gray-500">{{ $tagihan->bulan }}/{{ $tagihan->tahun }}</p>
                                                <p class="text-sm text-gray-700">Rp {{ number_format($tagihan->jumlah_tagihan, 0, ',', '.') }}</p>
                                            </div>
                                            <div class="text-right">
                                                @if($tagihan->status === 'lunas')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Lunas
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Belum Bayar
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.tagihan.index', ['pelanggan_id' => $pelanggan->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Lihat Semua Tagihan →
                                </a>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Belum ada tagihan</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

