<!DOCTYPE html>
<html lang="id">
 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Buku Tamu - Petugas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/js/app.js'])
</head>
 
<body class="bg-gray-100 min-h-screen py-10 px-4" 
style="background-image: url('{{ asset('images/back.jpg') }}'); background-size: cover;">
    <div class="max-w-7xl mx-auto space-y-6">
 
        <div class="text-center space-y-2">
            <h1 class="text-4xl font-semibold text-gray-900">Dashboard Buku Tamu</h1>
            <p class="text-sm text-gray-500">Kelola data tamu masuk — guru &amp; murid Mini Expo</p>
        </div>
 
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
        @endif
 
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-white border border-gray-200 rounded p-4">
                <p class="text-xs text-gray-500 mb-1">Total Tamu</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $tamus->count() }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded p-4">
                <p class="text-xs text-gray-500 mb-1">Guru</p>
                <p class="text-2xl font-semibold text-indigo-700">{{ $tamus->where('jenis_tamu','guru')->count() }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded p-4">
                <p class="text-xs text-gray-500 mb-1">Murid</p>
                <p class="text-2xl font-semibold text-green-700">{{ $tamus->where('jenis_tamu','murid')->count() }}</p>
            </div>
        </div>
 
        <div class="flex gap-3">
            <button id="btn_tambah" class="flex-1 bg-blue-600 hover:bg-blue-700 active:scale-[.98] text-white text-sm font-medium py-2.5 px-4 rounded-lg transition-all">
                + Tambah Tamu Baru
            </button>
            <a href="/export-tamu" class="flex items-center gap-2 bg-green-50 hover:bg-green-100 border border-green-200 text-green-700 text-sm font-medium py-2.5 px-4 rounded-lg transition-all whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                </svg>
                Export CSV
            </a>
        </div>
 
        <div id="form_area" class="hidden bg-gray-50 border border-gray-200 rounded p-6">
            <h2 class="text-base font-semibold text-blue-900 mb-5 flex items-center gap-2">
                <span class="w-1 h-4 bg-blue-600 rounded-full inline-block"></span>
                Isi Data Tamu
            </h2>
 
            <form action="/petugas_tamu" method="POST" id="main_form">
                @csrf
                <input type="hidden" name="tanda_tangan" id="input_tanda_tangan" required>
 
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Jenis Tamu</label>
                        <select name="jenis_tamu" id="jenis_tamu" class="w-full border border-gray-300 bg-white text-sm text-gray-800 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                            <option value="">Pilih...</option>
                            <option value="guru">Guru</option>
                            <option value="murid">Murid</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="nama" placeholder="Masukkan nama..." class="w-full border border-gray-300 bg-white text-sm text-gray-800 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    </div>
                </div>
 
                <div id="field_guru" class="hidden mb-4">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Mata Pelajaran</label>
                    <input type="text" name="mapel" placeholder="cth. Matematika" class="w-full border border-gray-300 bg-white text-sm text-gray-800 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
 
                <div id="field_murid" class="hidden mb-4 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Kelas</label>
                        <select name="kelas" class="w-full border border-gray-300 bg-white text-sm text-gray-800 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Jurusan</label>
                        <select name="jurusan" class="w-full border border-gray-300 bg-white text-sm text-gray-800 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="Akuntansi 1">Akuntansi 1</option>
                            <option value="Akuntansi 2">Akuntansi 2</option>
                            <option value="Akuntansi 3">Akuntansi 3</option>
                            <option value="Akuntansi 4">Akuntansi 4</option>
                            <option value="Rekayasa Perangkat Lunak 1">Rekayasa Perangkat Lunak 1</option>
                            <option value="Rekayasa Perangkat Lunak 2">Rekayasa Perangkat Lunak 2</option>
                            <option value="Desain Komunikasi Visual 1">Desain Komunikasi Visual 1</option>
                            <option value="Desain Komunikasi Visual 2">Desain Komunikasi Visual 2</option>
                            <option value="Desain Komunikasi Visual 3">Desain Komunikasi Visual 3</option>
                            <option value="Teknik Komputer dan Jaringan">Teknik Komputer dan Jaringan 1</option>
                            <option value="Teknik Komputer dan Jaringan">Teknik Komputer dan Jaringan 2</option>
                            <option value="Teknik Komputer dan Jaringan">Teknik Komputer dan Jaringan 3</option>
                            <option value="Perhotelan 1">Perhotelan 1</option>
                            <option value="Perhotelan 2">Perhotelan 2</option>
                            <option value="Perhotelan 3">Perhotelan 3</option>
                            <option value="Perhotelan 4">Perhotelan 4</option>
                            <option value="Managemen Logistik 1">Managemen Logistik 1</option>
                            <option value="Managemen Logistik 2">Managemen Logistik 2</option>
                            <option value="Managemen Logistik 3">Managemen Logistik 3</option>
                            <option value="Managemen Logistik 4">Managemen Logistik 4</option>
                            <option value="Managemen Perkantoran 1">Managemen Perkantoran 1</option>
                            <option value="Managemen Perkantoran 2">Managemen Perkantoran 2</option>
                            <option value="Managemen Perkantoran 3">Managemen Perkantoran 3</option>
                            <option value="Managemen Perkantoran 4">Managemen Perkantoran 4</option>
                            <option value="Produksi dan Siaran Program Televisi 1">Produksi dan Siaran Program Televisi 1</option>
                            <option value="Produksi dan Siaran Program Televisi 2">Produksi dan Siaran Program Televisi 2</option>
                            <option value="Produksi dan Siaran Program Televisi 3">Produksi dan Siaran Program Televisi 3</option>
                            <option value="Produksi dan Siaran Program Televisi 4">Produksi dan Siaran Program Televisi 4</option>
                            <option value="Bisnis Digital 1">Bisnis Digital 1</option>
                            <option value="Bisnis Digital 2">Bisnis Digital 2</option>
                            <option value="Bisnis Digital 3">Bisnis Digital 3</option>
                            <option value="Bisnis Digital 4">Bisnis Digital 4</option>
                        </select>
                    </div>
                </div>

                <div id="status_ttd" class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400 inline-block flex-shrink-0"></span>
                    <span class="text-xs text-amber-800 font-medium">Menunggu tanda tangan di tablet...</span>
                </div>
 
                <button type="submit" id="btn_simpan" disabled
                    class="w-full bg-gray-200 text-gray-400 text-sm font-medium py-2.5 rounded-lg cursor-not-allowed transition-all">
                    Simpan Data Tamu
                </button>
            </form>
        </div>

        <div class="bg-white border border-gray-200 rounded overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-800">Daftar Tamu Masuk</h2>
                <span class="text-xs text-gray-500 bg-gray-100 px-4 py-2 rounded">{{ $tamus->count() }} entri</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="text-left text-xs font-medium text-gray-500 px-5 py-3 border-b border-gray-100">Nama</th>
                            <th class="text-left text-xs font-medium text-gray-500 px-5 py-3 border-b border-gray-100">Jenis</th>
                            <th class="text-left text-xs font-medium text-gray-500 px-5 py-3 border-b border-gray-100">Detail</th>
                            <th class="text-left text-xs font-medium text-gray-500 px-5 py-3 border-b border-gray-100">TTD</th>
                            <th class="text-left text-xs font-medium text-gray-500 px-5 py-3 border-b border-gray-100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($tamus as $t)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3 text-gray-900 font-medium">{{ $t->nama }}</td>
                            <td class="px-5 py-3">
                                @if($t->jenis_tamu === 'guru')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">Guru</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Murid</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                @if($t->jenis_tamu === 'guru')
                                    <span class="inline-block bg-amber-50 text-amber-800 text-xs px-2 py-0.5 rounded">{{ $t->mapel }}</span>
                                @else
                                    <span class="text-xs text-gray-500"> {{ $t->kelas_jurusan }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <div class="w-14 h-8 border border-gray-200 rounded bg-white overflow-hidden flex items-center justify-center">
                                    <img src="{{ $t->tanda_tangan }}" class="max-h-full max-w-full object-contain">
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <form action="/petugas_tamu/{{ $t->id }}" method="POST" onsubmit="return confirm('Hapus tamu ini?');">
                                    @csrf @method('DELETE')
                                    <button class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
 
    </div>
 
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const btnTambah = document.getElementById('btn_tambah');
        const formArea = document.getElementById('form_area');
        const jenisTamu = document.getElementById('jenis_tamu');
        const fieldGuru = document.getElementById('field_guru');
        const fieldMurid = document.getElementById('field_murid');
 
        jenisTamu.addEventListener('change', function() {
            fieldGuru.classList.add('hidden');
            fieldMurid.classList.add('hidden');
            if (this.value === 'guru') fieldGuru.classList.remove('hidden');
            if (this.value === 'murid') fieldMurid.classList.remove('hidden');
        });
 
        btnTambah.addEventListener('click', function() {
            formArea.classList.remove('hidden');
            btnTambah.classList.add('hidden');
 
            axios.post('/trigger-canvas').then(() => {
                startPollingTtd();
            });
        });
 
        function startPollingTtd() {
            let polling = setInterval(() => {
                axios.get('/cek-ttd-laptop').then(res => {
                    if (res.data.ttd) {
                        document.getElementById('input_tanda_tangan').value = res.data.ttd;
                        document.getElementById('status_ttd').innerHTML = `
                            <span class="w-2 h-2 rounded-full bg-green-500 inline-block flex-shrink-0"></span>
                            <span class="text-xs text-green-800 font-medium">Tanda tangan siap!</span>
                        `;
 
                        const btnSimpan = document.getElementById('btn_simpan');
                        btnSimpan.removeAttribute('disabled');
                        btnSimpan.classList.remove('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
                        btnSimpan.classList.add('bg-blue-600', 'hover:bg-blue-700', 'text-white', 'cursor-pointer', 'active:scale-[.98]');
 
                        clearInterval(polling);
                    }
                });
            }, 1500);
        }
    </script>
</body>
 
</html>