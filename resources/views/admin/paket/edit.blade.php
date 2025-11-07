<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Paket
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.paket.update', $paket) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama Paket -->
                            <div>
                                <x-input-label for="nama_paket" :value="__('Nama Paket')" />
                                <x-text-input id="nama_paket" class="block mt-1 w-full" type="text" name="nama_paket" :value="old('nama_paket', $paket->nama_paket)" required autofocus />
                                <x-input-error :messages="$errors->get('nama_paket')" class="mt-2" />
                            </div>

                            <!-- Kecepatan -->
                            <div>
                                <x-input-label for="kecepatan" :value="__('Kecepatan')" />
                                <x-text-input id="kecepatan" class="block mt-1 w-full" type="text" name="kecepatan" :value="old('kecepatan', $paket->kecepatan)" placeholder="Contoh: 10 Mbps" required />
                                <x-input-error :messages="$errors->get('kecepatan')" class="mt-2" />
                            </div>

                            <!-- Harga Bulanan -->
                            <div>
                                <x-input-label for="harga_bulanan" :value="__('Harga Bulanan (Rp)')" />
                                <x-text-input id="harga_bulanan" class="block mt-1 w-full" type="number" name="harga_bulanan" :value="old('harga_bulanan', (int) $paket->harga_bulanan)" min="0" step="1" required />
                                <x-input-error :messages="$errors->get('harga_bulanan')" class="mt-2" />
                            </div>

                            <!-- Harga Pemasangan -->
                            <div>
                                <x-input-label for="harga_pemasangan" :value="__('Harga Pemasangan (Rp)')" />
                                <x-text-input id="harga_pemasangan" class="block mt-1 w-full" type="number" name="harga_pemasangan" :value="old('harga_pemasangan', (int) $paket->harga_pemasangan)" min="0" step="1" />
                                <x-input-error :messages="$errors->get('harga_pemasangan')" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="aktif" {{ old('status', $paket->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="tidak_aktif" {{ old('status', $paket->status) === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mt-6">
                            <x-input-label for="deskripsi" :value="__('Deskripsi')" />
                            <textarea id="deskripsi" name="deskripsi" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('deskripsi', $paket->deskripsi) }}</textarea>
                            <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.paket.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">
                                Batal
                            </a>
                            <x-primary-button>
                                Perbarui
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

