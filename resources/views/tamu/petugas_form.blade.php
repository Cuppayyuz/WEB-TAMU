<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Tamu - Petugas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50 min-h-screen py-8 px-4">

<div class="max-w-3xl mx-auto">
    
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800">📝 Form Penerima Tamu</h1>
        <p class="text-gray-600 mt-2">Session ID: <span class="font-bold text-blue-600">#{{ $tamu->id }}</span></p>
    </div>

    <!-- Status TTD Card -->
    <div class="mb-6 p-6 bg-white rounded-2xl shadow-lg">
        <div id="status-ttd" class="text-center">
            <div class="bg-red-100 border-2 border-red-400 text-red-700 px-6 py-4 rounded-lg font-bold text-lg animate-pulse">
                ⏳ Menunggu Tanda Tangan Tamu...
            </div>
            <p class="text-sm text-gray-500 mt-3">Pastikan tablet tamu sudah aktif dan siap menandatangani</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <form id="formPetugas">
            @csrf
            
            <!-- Jenis Tamu -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Tamu *</label>
                <select id="jenis_tamu" name="jenis_tamu" 
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                        required onchange="toggleFields()">
                    <option value="">-- Pilih --</option>
                    <option value="guru">Guru</option>
                    <option value="siswa">Murid/Siswa</option>
                </select>
            </div>

            <!-- Nama Lengkap -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap *</label>
                <input type="text" name="nama" id="nama" 
                       class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       required>
            </div>

            <!-- Guru Field -->
            <div id="field_guru" class="hidden mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Mata Pelajaran</label>
                <input type="text" name="mapel" id="mapel" 
                       class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Siswa Fields -->
            <div id="field_siswa" class="hidden mb-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kelas</label>
                    <input type="text" name="kelas" id="kelas" 
                           class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jurusan</label>
                    <input type="text" name="jurusan" id="jurusan" 
                           class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Hidden Signature -->
            <input type="hidden" id="signature_base64" name="signature" value="">

            <!-- Submit Button -->
            <button type="submit" id="btn-submit" 
                    class="w-full mt-8 px-6 py-3 bg-gray-400 text-white rounded-lg font-semibold cursor-not-allowed transition text-lg" 
                    disabled>
                ⏳ Tunggu Tanda Tangan Tamu...
            </button>
        </form>

        <div class="mt-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
            <p class="text-sm text-blue-800">
                <strong>ℹ️ Catatan:</strong> Form akan otomatis aktif setelah tamu menandatangani di tablet.
            </p>
        </div>
    </div>

</div>

<!-- Canvas JS (untuk export window scope) -->
@vite('resources/js/canvas-sync.js')

<script>
    const sessionId = '{{ $tamu->id }}';
    let ttdReceived = false;

    console.log('📄 Petugas form loaded, sessionId:', sessionId);

    function toggleFields() {
        const jenis = document.getElementById('jenis_tamu').value;
        document.getElementById('field_guru').classList.toggle('hidden', jenis !== 'guru');
        document.getElementById('field_siswa').classList.toggle('hidden', jenis !== 'siswa');
    }

    // POLLING: Cek apakah tamu sudah TTD
    const ttdCheckInterval = setInterval(async () => {
        if (ttdReceived) return; // Stop jika sudah terima

        try {
            const response = await fetch(`/api/cek-ttd-petugas/${sessionId}`);
            const data = await response.json();

            if (data.sudah_ttd && !ttdReceived) {
                ttdReceived = true;
                clearInterval(ttdCheckInterval);
                
                console.log('✅ TTD Received!');

                // Update Status Card
                document.getElementById('status-ttd').innerHTML = `
                    <div class="bg-green-100 border-2 border-green-400 text-green-700 px-6 py-4 rounded-lg font-bold text-lg">
                        ✅ Tanda Tangan Tamu Sudah Diterima!
                    </div>
                `;

                // Enable Submit Button
                const btn = document.getElementById('btn-submit');
                btn.disabled = false;
                btn.className = 'w-full mt-8 px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition cursor-pointer text-lg';
                btn.innerHTML = '✅ Selesai & Cetak Struk';

                // Set hidden signature value (TTD sudah tersimpan dari tablet)
                document.getElementById('signature_base64').value = 'received_from_tablet';
            }
        } catch (error) {
            console.error('❌ Polling error:', error);
        }
    }, 1500);

    // Form submission
    document.getElementById('formPetugas').addEventListener('submit', async (e) => {
        e.preventDefault();

        if (!ttdReceived) {
            alert('❌ Tanda tangan tamu belum diterima!');
            return;
        }

        const formData = new FormData(e.target);
        const data = {
            nama: formData.get('nama'),
            jenis_tamu: formData.get('jenis_tamu'),
            mapel: formData.get('mapel'),
            kelas: formData.get('kelas'),
            jurusan: formData.get('jurusan'),
            signature: formData.get('signature'),
            _token: document.querySelector('meta[name="csrf-token"]').content
        };

        const btn = document.getElementById('btn-submit');
        btn.disabled = true;
        btn.innerHTML = '⏳ Sedang menyimpan data...';

        try {
            const response = await fetch(`/tamu/simpan-final/${sessionId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': data._token
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok && result.success) {
                alert('✅ Data berhasil disimpan!\n🖨️ Struk sedang dicetak...');
                
                // Redirect ke dashboard petugas
                setTimeout(() => {
                    window.location.href = '{{ route("tamu.index") }}';
                }, 2000);
            } else {
                alert('❌ Error: ' + (result.message || 'Gagal menyimpan'));
                btn.disabled = false;
                btn.innerHTML = '✅ Selesai & Cetak Struk';
            }
        } catch (error) {
            console.error('Error:', error);
            alert('❌ Error: ' + error.message);
            btn.disabled = false;
            btn.innerHTML = '✅ Selesai & Cetak Struk';
        }
    });

    console.log('✓ Petugas form script loaded');
</script>

</body>
</html>