<x-pelanggan-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Request Upgrade Paket
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if($permintaanMenunggu)
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-4">
                    <p class="text-sm text-yellow-800">
                        <strong>Peringatan:</strong> Anda masih memiliki permintaan upgrade yang menunggu persetujuan admin. 
                        Silakan tunggu hingga permintaan sebelumnya diproses.
                    </p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Informasi Paket Saat Ini -->
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                        <h3 class="text-sm font-semibold text-blue-900 mb-2">Paket Saat Ini</h3>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium">{{ $pelanggan->paket->nama_paket }} - {{ $pelanggan->paket->kecepatan }}</p>
                            <p>Rp {{ number_format($pelanggan->paket->harga_bulanan, 0, ',', '.') }}/bulan</p>
                        </div>
                    </div>

                    <form action="{{ route('pelanggan.permintaan-upgrade.store') }}" method="POST">
                        @csrf

                        <div class="space-y-6">
                            <!-- Paket Baru -->
                            <div>
                                <x-input-label for="paket_baru_id" :value="__('Pilih Paket Baru')" />
                                <select id="paket_baru_id" name="paket_baru_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required @disabled($permintaanMenunggu)>
                                    <option value="">Pilih Paket</option>
                                    @foreach($paket as $p)
                                        <option value="{{ $p->id }}" {{ old('paket_baru_id') == $p->id ? 'selected' : '' }}>
                                            {{ $p->nama_paket }} - {{ $p->kecepatan }} (Rp {{ number_format($p->harga_bulanan, 0, ',', '.') }}/bulan)
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-sm text-gray-500">Pilih paket yang ingin Anda upgrade</p>
                                <x-input-error :messages="$errors->get('paket_baru_id')" class="mt-2" />
                            </div>

                            <!-- Alasan (Opsional) -->
                            <div>
                                <x-input-label for="alasan" :value="__('Alasan (Opsional)')" />
                                <textarea id="alasan" name="alasan" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Alasan mengapa Anda ingin upgrade paket (opsional)" @disabled($permintaanMenunggu)>{{ old('alasan') }}</textarea>
                                <x-input-error :messages="$errors->get('alasan')" class="mt-2" />
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mt-6">
                            <p class="text-sm text-blue-800">
                                <strong>Catatan:</strong> Setelah permintaan upgrade dikirim, admin akan memproses permintaan Anda. 
                                Jika disetujui, paket Anda akan diubah dan tagihan upgrade akan dibuat (selisih harga bulanan).
                            </p>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('pelanggan.permintaan-upgrade.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">
                                Batal
                            </a>
                            <x-primary-button :disabled="$permintaanMenunggu">
                                Kirim Permintaan Upgrade
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-pelanggan-layout>

