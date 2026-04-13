<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Petugas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-2xl relative">
        
        <div id="status-ttd" class="absolute top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded shadow flex items-center gap-2 font-bold animate-pulse">
            ⏳ Menunggu Tamu TTD...
        </div>

        <h2 class="text-2xl font-bold mb-6 mt-4">Isi Data Tamu</h2>

        <form id="formPetugas" action="{{ route('tamu.simpanFinal', $tamu->id) }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Jenis Tamu</label>
                <select id="jenis_tamu" name="jenis_tamu" class="w-full border rounded px-3 py-2" required onchange="toggleFields()">
                    <option value="" disabled selected>Pilih...</option>
                    <option value="guru">Guru</option>
                    <option value="siswa">Murid</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Nama Lengkap</label>
                <input type="text" name="nama" class="w-full border rounded px-3 py-2" required>
            </div>

            <div id="field_guru" class="hidden mb-4">
                <label class="block text-gray-700 font-bold mb-2">Mata Pelajaran</label>
                <input type="text" name="mapel" class="w-full border rounded px-3 py-2">
            </div>

            <div id="field_siswa" class="hidden mb-4 flex gap-4">
                <div class="w-1/2">
                    <label class="block text-gray-700 font-bold mb-2">Kelas</label>
                    <input type="text" name="kelas" class="w-full border rounded px-3 py-2">
                </div>
                <div class="w-1/2">
                    <label class="block text-gray-700 font-bold mb-2">Jurusan</label>
                    <input type="text" name="jurusan" class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <button type="button" id="btn-submit" onclick="cobaSubmit()" class="w-full bg-gray-400 text-white font-bold py-3 rounded cursor-not-allowed transition mt-4" disabled>
                Cetak Struk (Tunggu TTD Tamu)
            </button>
        </form>
    </div>

    <script>
        function toggleFields() {
            const jenis = document.getElementById('jenis_tamu').value;
            document.getElementById('field_guru').classList.toggle('hidden', jenis !== 'guru');
            document.getElementById('field_siswa').classList.toggle('hidden', jenis !== 'siswa');
        }

        let isTtdDone = false;
        const btnSubmit = document.getElementById('btn-submit');
        const statusEl = document.getElementById('status-ttd');

        // POLLING: Cek API setiap 1.5 detik
        setInterval(async () => {
            if(isTtdDone) return; // Stop cek kalau sudah TTD

            try {
                let response = await fetch('/api/cek-ttd-petugas/{{ $tamu->id }}');
                let data = await response.json();

                if(data.sudah_ttd) {
                    isTtdDone = true;
                    // Ubah UI Indikator jadi Hijau
                    statusEl.className = "absolute top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded shadow font-bold";
                    statusEl.innerHTML = "✅ TTD Diterima!";
                    
                    // Aktifkan Tombol Cetak
                    btnSubmit.className = "w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded transition shadow mt-4";
                    btnSubmit.innerHTML = "Simpan Data & Cetak Struk";
                    btnSubmit.disabled = false;
                }
            } catch (error) {}
        }, 1500);

        function cobaSubmit() {
            if(!isTtdDone) {
                alert("Tamu belum memberikan tanda tangan di tablet!");
            } else {
                document.getElementById('formPetugas').submit();
            }
        }
    </script>
</body>
</html>