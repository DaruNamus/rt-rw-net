<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Permintaan Upgrade
            </h2>
            <a href="{{ route('admin.permintaan-upgrade.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-md transition ease-in-out duration-150">
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
                <!-- Informasi Permintaan Upgrade -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Permintaan Upgrade</h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500">Pelanggan</p>
                                <p class="font-medium text-gray-900">{{ $permintaanUpgrade->pelanggan->user->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-medium text-gray-900">{{ $permintaanUpgrade->pelanggan->user->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Paket Saat Ini</p>
                                <p class="font-medium text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $permintaanUpgrade->paketLama->nama_paket }}
                                    </span>
                                    - {{ $permintaanUpgrade->paketLama->kecepatan }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1">Rp {{ number_format($permintaanUpgrade->paketLama->harga_bulanan, 0, ',', '.') }}/bulan</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Paket yang Diminta</p>
                                <p class="font-medium text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $permintaanUpgrade->paketBaru->nama_paket }}
                                    </span>
                                    - {{ $permintaanUpgrade->paketBaru->kecepatan }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1">Rp {{ number_format($permintaanUpgrade->paketBaru->harga_bulanan, 0, ',', '.') }}/bulan</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Selisih Harga Bulanan</p>
                                @php
                                    $selisih = $permintaanUpgrade->paketBaru->harga_bulanan - $permintaanUpgrade->paketLama->harga_bulanan;
                                @endphp
                                @if($selisih > 0)
                                    <p class="font-medium text-green-600 text-lg">+Rp {{ number_format($selisih, 0, ',', '.') }}/bulan</p>
                                @elseif($selisih < 0)
                                    <p class="font-medium text-red-600 text-lg">Rp {{ number_format($selisih, 0, ',', '.') }}/bulan</p>
                                @else
                                    <p class="font-medium text-gray-600 text-lg">Rp 0/bulan</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Tanggal Request</p>
                                <p class="font-medium text-gray-900">{{ $permintaanUpgrade->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Status</p>
                                @if($permintaanUpgrade->status === 'disetujui')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Disetujui
                                    </span>
                                @elseif($permintaanUpgrade->status === 'menunggu')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Menunggu
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Ditolak
                                    </span>
                                @endif
                            </div>
                            @if($permintaanUpgrade->alasan)
                                <div>
                                    <p class="text-sm text-gray-500">Alasan Pelanggan</p>
                                    <p class="font-medium text-gray-900">{{ $permintaanUpgrade->alasan }}</p>
                                </div>
                            @endif
                            @if($permintaanUpgrade->diprosesOleh)
                                <div>
                                    <p class="text-sm text-gray-500">Diproses Oleh</p>
                                    <p class="font-medium text-gray-900">{{ $permintaanUpgrade->diprosesOleh->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $permintaanUpgrade->diproses_pada->format('d M Y H:i') }}</p>
                                </div>
                            @endif
                            @if($permintaanUpgrade->catatan_admin)
                                <div>
                                    <p class="text-sm text-gray-500">Catatan Admin</p>
                                    <p class="font-medium text-gray-900">{{ $permintaanUpgrade->catatan_admin }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Form Setujui/Tolak -->
                @if($permintaanUpgrade->status === 'menunggu')
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Permintaan Upgrade</h3>
                            
                            <!-- Info Tagihan yang Akan Dibuat -->
                            <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-4">
                                <p class="text-sm font-medium text-blue-900 mb-2">Informasi Tagihan Upgrade:</p>
                                @php
                                    $selisih = $permintaanUpgrade->paketBaru->harga_bulanan - $permintaanUpgrade->paketLama->harga_bulanan;
                                @endphp
                                <p class="text-sm text-blue-800">
                                    Jika disetujui, akan dibuat tagihan upgrade sebesar 
                                    <strong>Rp {{ number_format(max(0, $selisih), 0, ',', '.') }}</strong> 
                                    (selisih harga bulanan) untuk bulan ini.
                                </p>
                            </div>

                            <!-- Form Setujui -->
                            <form action="{{ route('admin.permintaan-upgrade.setujui', $permintaanUpgrade) }}" method="POST" class="mb-4">
                                @csrf
                                <div class="mb-4">
                                    <x-input-label for="catatan_setujui" :value="__('Catatan (Opsional)')" />
                                    <textarea id="catatan_setujui" name="catatan_admin" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Catatan untuk pelanggan (opsional)">{{ old('catatan_admin') }}</textarea>
                                </div>
                                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-black font-medium py-2.5 px-4 rounded-md transition ease-in-out duration-150" onclick="return confirm('Apakah Anda yakin ingin menyetujui permintaan upgrade ini? Paket pelanggan akan diubah dan tagihan upgrade akan dibuat.');">
                                    Setujui Permintaan Upgrade
                                </button>
                            </form>

                            <!-- Form Tolak -->
                            <form action="{{ route('admin.permintaan-upgrade.tolak', $permintaanUpgrade) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <x-input-label for="catatan_tolak" :value="__('Alasan Penolakan')" />
                                    <textarea id="catatan_tolak" name="catatan_admin" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Wajib diisi saat menolak permintaan upgrade" required>{{ old('catatan_admin') }}</textarea>
                                    <x-input-error :messages="$errors->get('catatan_admin')" class="mt-2" />
                                </div>
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2.5 px-4 rounded-md transition ease-in-out duration-150" onclick="return confirm('Apakah Anda yakin ingin menolak permintaan upgrade ini?');">
                                    Tolak Permintaan Upgrade
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Permintaan</h3>
                            <p class="text-gray-600 text-sm">
                                Permintaan upgrade ini sudah diproses pada 
                                <strong>{{ $permintaanUpgrade->diproses_pada->format('d M Y H:i') }}</strong>
                                oleh <strong>{{ $permintaanUpgrade->diprosesOleh->name }}</strong>.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>

