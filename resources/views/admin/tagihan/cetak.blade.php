<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pembayaran - {{ $tagihan->tagihan_id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none;
            }
        }
        body {
            font-family: 'Courier New', Courier, monospace;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white p-8 max-w-lg w-full shadow-lg border-2 border-dashed border-gray-300 relative print:shadow-none print:border-none print:w-full print:max-w-none print:p-0">
        
        <!-- Watermark LUNAS -->
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-10 pointer-events-none print:opacity-5">
            <span class="text-9xl font-bold text-gray-500 transform -rotate-45 block border-4 border-gray-500 p-4 rounded-xl">LUNAS</span>
        </div>

        <!-- Header -->
        <div class="text-center border-b-2 border-gray-800 pb-4 mb-6">
            <h1 class="text-2xl font-bold uppercase tracking-widest text-gray-900">RT-RW Net</h1>
            <p class="text-sm text-gray-600">Jl. Drebeg, Margorejo, Kec. Dawe, Kabupaten Kudus, Jawa Tengah 59353</p>
            <p class="text-sm text-gray-600">Telp: 0812-3456-7890</p>
        </div>

        <!-- Title -->
        <div class="text-center mb-6">
            <h2 class="text-xl font-bold border-2 border-gray-800 inline-block px-4 py-1">BUKTI TRANSAKSI</h2>
        </div>

        <!-- Transaction Info -->
        <div class="mb-6 text-sm">
            <div class="flex justify-between mb-1">
                <span class="text-gray-600">No. Tagihan:</span>
                <span class="font-bold">{{ $tagihan->tagihan_id }}</span>
            </div>
            <div class="flex justify-between mb-1">
                <span class="text-gray-600">Tanggal:</span>
                <span class="font-bold">{{ \Carbon\Carbon::parse($tagihan->updated_at)->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Admin:</span>
                <span class="font-bold">{{ auth()->user()->name ?? 'System' }}</span>
            </div>
        </div>

        <hr class="border-dashed border-gray-400 my-4">

        <!-- Customer Info -->
        <div class="mb-6 text-sm">
            <div class="mb-1">
                <span class="text-gray-600 block text-xs">PELANGGAN:</span>
                <span class="font-bold text-lg uppercase">{{ $tagihan->pelanggan->user->name ?? 'N/A' }}</span>
            </div>
            <div class="mb-1">
                <span class="text-gray-600">ID Pelanggan:</span>
                <span class="font-mono">{{ $tagihan->pelanggan_id }}</span>
            </div>
            <div>
                <span class="text-gray-600">Alamat:</span>
                <span>{{ $tagihan->pelanggan->alamat }}</span>
            </div>
        </div>

        <hr class="border-dashed border-gray-400 my-4">

        <!-- Payment Details -->
        <div class="mb-6">
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-1">Keterangan:</p>
                <p class="font-medium text-gray-900">
                    Tagihan {{ ucfirst($tagihan->jenis_tagihan) }}
                    <span class="block text-xs text-gray-500">
                        {{ \Carbon\Carbon::create()->month($tagihan->bulan)->locale('id')->translatedFormat('F') }} {{ $tagihan->tahun }}
                    </span>
                    <span class="block text-xs text-gray-500">({{ $tagihan->paket->nama_paket ?? 'Paket' }} - {{ $tagihan->paket->kecepatan ?? '' }})</span>
                </p>
                @if($tagihan->keterangan)
                    <p class="text-xs text-gray-500 italic mt-1">Note: {{ $tagihan->keterangan }}</p>
                @endif
            </div>

            <div class="bg-gray-100 p-4 rounded-lg flex justify-between items-center print:bg-transparent print:p-0 print:border-y-2 print:border-gray-800 print:rounded-none">
                <span class="font-bold text-gray-700 uppercase">Total Tagihan</span>
                <span class="font-bold text-2xl text-gray-900">Rp {{ number_format($tagihan->jumlah_tagihan, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-12 pt-4 border-t-2 border-gray-200 print:mt-8">
            <p class="text-xs text-gray-500 italic mb-2">Transaksi ini sah dan telah lunas.</p>
            <p class="text-xs text-gray-400">Dicetak pada {{ now()->format('d/m/Y H:i') }}</p>
        </div>

        <!-- Buttons -->
        <div class="mt-8 flex justify-center space-x-4 no-print">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow-lg flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Bukti
            </button>
            <button onclick="window.close()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded shadow-lg">
                Tutup
            </button>
        </div>

    </div>

    <script>
        // Auto print when window opens
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
