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

<body class="bg-gray-100 p-8">
    <div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold mb-6 text-center text-blue-600">Dashboard Penerima Tamu</h1>

        @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-blue-600">Dashboard Tamu</h1>
            <a href="/export-tamu" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Export Excel (.csv)
            </a>
        </div>
    
        <button id="btn_tambah" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-6 w-full">
            + Tambah Tamu Baru
        </button>

        <div id="form_area" class="hidden mb-8 border border-blue-200 p-4 rounded bg-blue-50">
            <h2 class="text-xl font-bold mb-4">Isi Data Tamu</h2>
            <form action="/petugas_tamu" method="POST" id="main_form">
                @csrf
                <input type="hidden" name="tanda_tangan" id="input_tanda_tangan" required>

                <div class="mb-4">
                    <label class="block mb-1 font-semibold">Jenis Tamu</label>
                    <select name="jenis_tamu" id="jenis_tamu" class="w-full border p-2 rounded" required>
                        <option value="">Pilih...</option>
                        <option value="guru">Guru</option>
                        <option value="murid">Murid</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-semibold">Nama Lengkap</label>
                    <input type="text" name="nama" class="w-full border p-2 rounded" required>
                </div>

                <div id="field_guru" class="hidden mb-4">
                    <label class="block mb-1 font-semibold">Mata Pelajaran</label>
                    <input type="text" name="mapel" class="w-full border p-2 rounded">
                </div>

                <div id="field_murid" class="hidden mb-4 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-semibold">Kelas</label>
                        <select name="kelas" class="w-full border p-2 rounded">
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 font-semibold">Jurusan</label>
                        <select name="jurusan" class="w-full border p-2 rounded">
                            <option value="Akuntansi 1">Akuntansi 1</option>
                            <option value="Akuntansi 2">Akuntansi 2</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4 p-3 bg-gray-200 rounded text-center" id="status_ttd">
                    <span class="text-red-500 font-bold italic">Selesaikan tanda tangan di tablet...</span>
                </div>

                <button type="submit" id="btn_simpan" disabled class="bg-gray-400 text-white font-bold py-2 px-4 rounded w-full cursor-not-allowed">
                    Simpan Data Tamu
                </button>
            </form>
        </div>

        <h2 class="text-2xl font-bold mb-4">Daftar Tamu Masuk</h2>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">Nama</th>
                    <th class="border p-2">Jenis</th>
                    <th class="border p-2">Detail</th>
                    <th class="border p-2">TTD</th>
                    <th class="border p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tamus as $t)
                <tr>
                    <td class="border p-2">{{ $t->nama }}</td>
                    <td class="border p-2">{{ ucfirst($t->jenis_tamu) }}</td>
                    <td class="border p-2">
                        {{ $t->jenis_tamu == 'guru' ? 'Mapel: '.$t->mapel : 'Kls: '.$t->kelas_jurusan }}
                    </td>
                    <td class="border p-2">
                        <img src="{{ $t->tanda_tangan }}" class="h-10 border bg-white">
                    </td>
                    <td class="border p-2">
                        <form action="/petugas_tamu/{{ $t->id }}" method="POST" onsubmit="return confirm('Hapus tamu ini?');">
                            @csrf @method('DELETE')
                            <button class="bg-red-500 text-white px-3 py-1 rounded text-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const btnTambah = document.getElementById('btn_tambah');
        const formArea = document.getElementById('form_area');
        const jenisTamu = document.getElementById('jenis_tamu');
        const fieldGuru = document.getElementById('field_guru');
        const fieldMurid = document.getElementById('field_murid');

        // Toggle Form Fields
        jenisTamu.addEventListener('change', function() {
            fieldGuru.classList.add('hidden');
            fieldMurid.classList.add('hidden');
            if (this.value === 'guru') fieldGuru.classList.remove('hidden');
            if (this.value === 'murid') fieldMurid.classList.remove('hidden');
        });

        // Logika Tombol Sesuai Request
        btnTambah.addEventListener('click', function() {
            // 1. Munculkan Form
            formArea.classList.remove('hidden');
            // 2. Hilangkan Tombol Tambah
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
                        document.getElementById('status_ttd').innerHTML = "<span class='text-green-600 font-bold'>✓ Tanda Tangan Siap!</span>";

                        let btnSimpan = document.getElementById('btn_simpan');
                        btnSimpan.removeAttribute('disabled');
                        btnSimpan.classList.remove('bg-gray-400', 'cursor-not-allowed');
                        btnSimpan.classList.add('bg-blue-600', 'hover:bg-blue-700');

                        clearInterval(polling);
                    }
                });
            }, 1500);
        }
    </script>
</body>

</html>