<x-pelanggan-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Upload Bukti Pembayaran
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Informasi Tagihan -->
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                        <h3 class="text-sm font-semibold text-blue-900 mb-2">Informasi Tagihan</h3>
                        <div class="grid grid-cols-2 gap-2 text-sm text-blue-800">
                            <div>
                                <span class="font-medium">Paket:</span> {{ $tagihan->paket->nama_paket }}
                            </div>
                            <div>
                                <span class="font-medium">Periode:</span> {{ \Carbon\Carbon::create()->month($tagihan->bulan)->locale('id')->translatedFormat('F') }} {{ $tagihan->tahun }}
                            </div>
                            <div>
                                <span class="font-medium">Jumlah Tagihan:</span> Rp {{ number_format($tagihan->jumlah_tagihan, 0, ',', '.') }}
                            </div>
                            <div>
                                <span class="font-medium">Jatuh Tempo:</span> {{ \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->format('d M Y') }}
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('pelanggan.pembayaran.store', $tagihan) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="space-y-6">
                            <!-- Jumlah Bayar -->
                            <div>
                                <x-input-label for="jumlah_bayar" :value="__('Jumlah Bayar (Rp)')" />
                                <x-text-input id="jumlah_bayar" class="block mt-1 w-full" type="number" name="jumlah_bayar" :value="old('jumlah_bayar', $tagihan->jumlah_tagihan)" min="0" step="0.01" required />
                                <p class="mt-1 text-sm text-gray-500">Jumlah yang dibayar (biasanya sama dengan jumlah tagihan)</p>
                                <x-input-error :messages="$errors->get('jumlah_bayar')" class="mt-2" />
                            </div>

                            <!-- Tanggal Bayar -->
                            <div>
                                <x-input-label for="tanggal_bayar" :value="__('Tanggal Bayar')" />
                                <x-text-input id="tanggal_bayar" class="block mt-1 w-full" type="date" name="tanggal_bayar" :value="old('tanggal_bayar', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('tanggal_bayar')" class="mt-2" />
                            </div>

                            <!-- Bukti Pembayaran -->
                            <div>
                                <x-input-label for="bukti_pembayaran" :value="__('Bukti Pembayaran')" />
                                <input id="bukti_pembayaran" type="file" name="bukti_pembayaran" accept="image/jpeg,image/png,image/jpg" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required />
                                <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG (Maksimal 2MB)</p>
                                <x-input-error :messages="$errors->get('bukti_pembayaran')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('pelanggan.tagihan.show', $tagihan) }}" class="text-gray-600 hover:text-gray-800 mr-4">
                                Batal
                            </a>
                            <x-primary-button>
                                Upload Bukti Pembayaran
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-pelanggan-layout>

