<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tablet - Tanda Tangan Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4" data-session-id="">

    <!-- Standby Screen -->
    <div id="layar-standby" class="text-center">
        <h1 class="text-5xl font-bold text-gray-600 mb-4">📱 Tablet Tanda Tangan</h1>
        <p class="text-xl text-gray-500">Silakan Lapor ke Petugas untuk Memulai</p>
    </div>

    <!-- Signing Screen -->
    <div id="layar-ttd" class="hidden max-w-2xl w-full">
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="text-center mb-6">
                <h2 class="text-3xl font-bold text-gray-800">✍️ Silakan Tanda Tangan</h2>
                <p class="text-gray-600 mt-2">Tanda tangan Anda akan ditampilkan secara real-time di layar petugas</p>
            </div>

            <!-- Canvas Drawing Area -->
            <div class="mb-6 border-4 border-dashed border-blue-400 rounded-lg bg-white overflow-hidden shadow-lg">
                <canvas id="signature_canvas" class="w-full h-80 bg-white" style="display: block; cursor: crosshair;"></canvas>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button onclick="TabletCanvas.clear()"
                    class="flex-1 px-4 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition font-semibold text-lg shadow-md">
                    🗑️ Hapus
                </button>
                <button id="submit_signature_btn"
                    class="flex-1 px-4 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition font-semibold text-lg shadow-md">
                    ✅ Kirim Tanda Tangan
                </button>
            </div>

            <p class="text-center text-sm text-gray-500 mt-4">Setelah mengirim, tunggu instruksi berikutnya dari petugas</p>
        </div>
    </div>

    <!-- Success Message -->
    <div id="success-message" class="hidden fixed inset-0 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-2xl p-8 text-center shadow-2xl">
            <h2 class="text-4xl font-bold text-green-600 mb-4">✅ Terima Kasih!</h2>
            <p class="text-gray-600 mb-6">Tanda tangan Anda telah diterima</p>
            <p class="text-sm text-gray-500">Silakan tunggu instruksi dari petugas...</p>
        </div>
    </div>

    <!-- Reverb Setup - HARUS SEBELUM canvas-sync -->
    

    <!-- Canvas Sync JS - SESUDAH Echo ready -->
    @vite('resources/js/canvas-sync.js')

    <script>
        let currentSessionId = null;
        let isSigning = false;

        // POLLING: Cek sesi baru dari database
        // POLLING: Cek sesi baru dari database
        setInterval(async () => {
            if (isSigning) return;

            try {
                const response = await fetch('/api/cek-sesi-tablet');
                const data = await response.json();

                // Jika ada sesi draft baru
                if (data && data.id && data.tanda_tangan === null) {
                    currentSessionId = data.id;
                    isSigning = true;

                    // SET SESSION ID DULU - PENTING!
                    document.body.dataset.sessionId = currentSessionId;

                    // Show canvas screen
                    document.getElementById('layar-standby').classList.add('hidden');
                    document.getElementById('layar-ttd').classList.remove('hidden');

                    // Initialize TabletCanvas
                    TabletCanvas.init(currentSessionId);

                    console.log('✅ Canvas initialized for session:', currentSessionId);
                }
            } catch (error) {
                console.error('Error checking session:', error);
            }
        }, 2000);

        // Handle submit button
        document.getElementById('submit_signature_btn')?.addEventListener('click', async () => {
            if (!currentSessionId) {
                alert('Session tidak ditemukan!');
                return;
            }

            const base64 = TabletCanvas.getBase64();

            if (base64 === 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==') {
                alert('Tanda tangan tidak boleh kosong!');
                return;
            }

            try {
                console.log('🚀 Sending TTD to server...');

                const response = await fetch(`/api/simpan-ttd-tablet/${currentSessionId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        signature: base64
                    })
                });

                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);

                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    console.error('❌ Server returned non-JSON response');
                    const text = await response.text();
                    console.error('Response body:', text);
                    alert('❌ Server error: Invalid response');
                    return;
                }

                const result = await response.json();

                if (response.ok && result.success) {
                    // Show success screen
                    document.getElementById('layar-ttd').classList.add('hidden');
                    document.getElementById('success-message').classList.remove('hidden');

                    // Reset state
                    isSigning = false;
                    currentSessionId = null;

                    // Kembali ke standby setelah 3 detik
                    setTimeout(() => {
                        document.getElementById('success-message').classList.add('hidden');
                        document.getElementById('layar-standby').classList.remove('hidden');
                        TabletCanvas.clear();
                    }, 3000);

                } else {
                    alert('❌ Error: ' + result.message);
                }
            } catch (error) {
                console.error('❌ Error:', error);
                alert('❌ Error mengirim tanda tangan: ' + error.message);
            }
        });
    </script>

</body>

</html>