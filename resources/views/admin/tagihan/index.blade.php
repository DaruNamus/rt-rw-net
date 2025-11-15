<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manajemen Tagihan
            </h2>
            <a href="{{ route('admin.tagihan.create') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-md transition ease-in-out duration-150">
                Tambah Tagihan
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-4">
                    <form method="GET" action="{{ route('admin.tagihan.index') }}" class="flex items-end gap-4">
                        <div class="flex-1 min-w-[180px]">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                            <select name="status" class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm py-2">
                                <option value="">Semua Status</option>
                                <option value="belum_bayar" {{ request('status') == 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                                <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-[180px]">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Tagihan</label>
                            <select name="jenis_tagihan" class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm py-2">
                                <option value="">Semua Jenis</option>
                                <option value="bulanan" {{ request('jenis_tagihan') == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                <option value="pemasangan" {{ request('jenis_tagihan') == 'pemasangan' ? 'selected' : '' }}>Pemasangan</option>
                                <option value="upgrade" {{ request('jenis_tagihan') == 'upgrade' ? 'selected' : '' }}>Upgrade</option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Pelanggan</label>
                            <select name="pelanggan_id" class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm py-2">
                                <option value="">Semua Pelanggan</option>
                                @foreach($pelanggan as $p)
                                    <option value="{{ $p->pelanggan_id }}" {{ request('pelanggan_id') == $p->pelanggan_id ? 'selected' : '' }}>
                                        {{ $p->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-shrink-0">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5 invisible">&nbsp;</label>
                            <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-6 rounded-md transition ease-in-out duration-150 text-sm whitespace-nowrap">
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full divide-y divide-gray-200 border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-200">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-200">Pelanggan</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-200">Paket</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-200">Periode</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-200">Jumlah Tagihan</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-200">Jatuh Tempo</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-200">Jenis</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-200">Status</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-200">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($tagihan as $item)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-b border-gray-100">
                                            {{ ($tagihan->currentPage() - 1) * $tagihan->perPage() + $loop->iteration }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 border-b border-gray-100">
                                            {{ $item->pelanggan->user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 border-b border-gray-100">
                                            {{ $item->paket->nama_paket }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 border-b border-gray-100">
                                            {{ \Carbon\Carbon::create()->month($item->bulan)->locale('id')->translatedFormat('F') }} {{ $item->tahun }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-b border-gray-100">
                                            Rp {{ number_format($item->jumlah_tagihan, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 border-b border-gray-100">
                                            {{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-100">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded bg-blue-100 text-blue-800">
                                                {{ ucfirst($item->jenis_tagihan) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-100">
                                            @if($item->status === 'lunas')
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Lunas
                                                </span>
                                            @else
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Belum Bayar
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border-b border-gray-100">
                                            <div class="flex items-center justify-center space-x-3">
                                                <a href="{{ route('admin.tagihan.show', $item) }}" class="text-blue-600 hover:text-blue-900 hover:underline font-medium">
                                                    Detail
                                                </a>
                                                <span class="text-gray-300">|</span>
                                                <a href="{{ route('admin.tagihan.edit', $item) }}" class="text-indigo-600 hover:text-indigo-900 hover:underline font-medium">
                                                    Edit
                                                </a>
                                                <span class="text-gray-300">|</span>
                                                <form action="{{ route('admin.tagihan.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tagihan ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 hover:underline font-medium">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <p class="text-sm font-medium text-gray-500">Tidak ada tagihan yang ditemukan.</p>
                                                <a href="{{ route('admin.tagihan.create') }}" class="mt-4 text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                    Tambah Tagihan Pertama
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($tagihan->hasPages())
                        <div class="mt-6">
                            {{ $tagihan->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

