<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Tagihan Baru
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.tagihan.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Pelanggan -->
                            <div>
                                <x-input-label for="pelanggan_id" :value="__('Pelanggan')" />
                                <select id="pelanggan_id" name="pelanggan_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Pilih Pelanggan</option>
                                    @foreach($pelanggan as $p)
                                        <option value="{{ $p->pelanggan_id }}" {{ old('pelanggan_id') == $p->pelanggan_id ? 'selected' : '' }}>
                                            {{ $p->user->name }} - {{ $p->paket->nama_paket }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('pelanggan_id')" class="mt-2" />
                            </div>

                            <!-- Paket -->
                            <div>
                                <x-input-label for="paket_id" :value="__('Paket')" />
                                <select id="paket_id" name="paket_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Pilih Paket</option>
                                    @foreach($paket as $p)
                                        <option value="{{ $p->id }}" {{ old('paket_id') == $p->id ? 'selected' : '' }}>
                                            {{ $p->nama_paket }} - Rp {{ number_format($p->harga_bulanan, 0, ',', '.') }}/bulan
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('paket_id')" class="mt-2" />
                            </div>

                            <!-- Bulan -->
                            <div>
                                <x-input-label for="bulan" :value="__('Bulan')" />
                                <select id="bulan" name="bulan" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Pilih Bulan</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ old('bulan', date('n')) == $i ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($i)->locale('id')->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                                <x-input-error :messages="$errors->get('bulan')" class="mt-2" />
                            </div>

                            <!-- Tahun -->
                            <div>
                                <x-input-label for="tahun" :value="__('Tahun')" />
                                <x-text-input id="tahun" class="block mt-1 w-full" type="number" name="tahun" :value="old('tahun', date('Y'))" min="2020" max="2100" required />
                                <x-input-error :messages="$errors->get('tahun')" class="mt-2" />
                            </div>

                            <!-- Jumlah Tagihan -->
                            <div>
                                <x-input-label for="jumlah_tagihan" :value="__('Jumlah Tagihan (Rp)')" />
                                <x-text-input id="jumlah_tagihan" class="block mt-1 w-full" type="number" name="jumlah_tagihan" :value="old('jumlah_tagihan')" min="0" step="0.01" required />
                                <x-input-error :messages="$errors->get('jumlah_tagihan')" class="mt-2" />
                            </div>

                            <!-- Tanggal Jatuh Tempo -->
                            <div>
                                <x-input-label for="tanggal_jatuh_tempo" :value="__('Tanggal Jatuh Tempo')" />
                                <x-text-input id="tanggal_jatuh_tempo" class="block mt-1 w-full" type="date" name="tanggal_jatuh_tempo" :value="old('tanggal_jatuh_tempo')" required />
                                <x-input-error :messages="$errors->get('tanggal_jatuh_tempo')" class="mt-2" />
                            </div>

                            <!-- Jenis Tagihan -->
                            <div>
                                <x-input-label for="jenis_tagihan" :value="__('Jenis Tagihan')" />
                                <select id="jenis_tagihan" name="jenis_tagihan" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="bulanan" {{ old('jenis_tagihan') === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                    <option value="pemasangan" {{ old('jenis_tagihan') === 'pemasangan' ? 'selected' : '' }}>Pemasangan</option>
                                    <option value="upgrade" {{ old('jenis_tagihan') === 'upgrade' ? 'selected' : '' }}>Upgrade</option>
                                </select>
                                <x-input-error :messages="$errors->get('jenis_tagihan')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="mt-6">
                            <x-input-label for="keterangan" :value="__('Keterangan')" />
                            <textarea id="keterangan" name="keterangan" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('keterangan') }}</textarea>
                            <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.tagihan.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">
                                Batal
                            </a>
                            <x-primary-button>
                                Simpan
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

