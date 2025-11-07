<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Pelanggan
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

                    <form action="{{ route('admin.pelanggan.update', $pelanggan) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Akun</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nama -->
                                <div>
                                    <x-input-label for="name" :value="__('Nama Lengkap')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $pelanggan->user->name)" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <!-- Email -->
                                <div>
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $pelanggan->user->email)" required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <!-- Password -->
                                <div>
                                    <x-input-label for="password" :value="__('Password Baru (kosongkan jika tidak ingin mengubah)')" />
                                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <!-- Password Confirmation -->
                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password Baru')" />
                                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pelanggan</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Paket -->
                                <div>
                                    <x-input-label for="paket_id" :value="__('Paket')" />
                                    <select id="paket_id" name="paket_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        <option value="">Pilih Paket</option>
                                        @foreach($paket as $p)
                                            <option value="{{ $p->id }}" {{ old('paket_id', $pelanggan->paket_id) == $p->id ? 'selected' : '' }}>
                                                {{ $p->nama_paket }} - {{ $p->kecepatan }} (Rp {{ number_format($p->harga_bulanan, 0, ',', '.') }}/bulan)
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('paket_id')" class="mt-2" />
                                </div>

                                <!-- Tanggal Pemasangan -->
                                <div>
                                    <x-input-label for="tanggal_pemasangan" :value="__('Tanggal Pemasangan')" />
                                    <x-text-input id="tanggal_pemasangan" class="block mt-1 w-full" type="date" name="tanggal_pemasangan" :value="old('tanggal_pemasangan', $pelanggan->tanggal_pemasangan->format('Y-m-d'))" required />
                                    <x-input-error :messages="$errors->get('tanggal_pemasangan')" class="mt-2" />
                                </div>

                                <!-- Alamat -->
                                <div class="md:col-span-2">
                                    <x-input-label for="alamat" :value="__('Alamat')" />
                                    <textarea id="alamat" name="alamat" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('alamat', $pelanggan->alamat) }}</textarea>
                                    <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                                </div>

                                <!-- No. Telepon -->
                                <div>
                                    <x-input-label for="no_telepon" :value="__('No. Telepon')" />
                                    <x-text-input id="no_telepon" class="block mt-1 w-full" type="text" name="no_telepon" :value="old('no_telepon', $pelanggan->no_telepon)" required />
                                    <x-input-error :messages="$errors->get('no_telepon')" class="mt-2" />
                                </div>

                                <!-- Status -->
                                <div>
                                    <x-input-label for="status" :value="__('Status')" />
                                    <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        <option value="aktif" {{ old('status', $pelanggan->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="nonaktif" {{ old('status', $pelanggan->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('admin.pelanggan.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">
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

