<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tanda Tangan Tamu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        body { touch-action: none; } /* Mencegah layar tablet ikut ke-scroll saat ttd */
    </style>
</head>
<body class="bg-gray-800 h-screen flex items-center justify-center p-4 text-white">

    <div id="layar_tunggu" class="text-center">
        <h1 class="text-4xl font-bold mb-4 animate-pulse">Selamat Datang!</h1>
        <p class="text-xl text-gray-300">Mohon tunggu instruksi dari petugas...</p>
    </div>

    <div id="layar_canvas" class="hidden w-full max-w-2xl bg-white p-6 rounded-lg text-black text-center shadow-2xl">
        <h2 class="text-2xl font-bold mb-4 text-blue-600">Silakan Tanda Tangan</h2>
        
        <div class="border-4 border-gray-300 rounded-lg bg-gray-50 mb-4">
            <canvas id="signature-pad" class="w-full h-64 rounded-lg"></canvas>
        </div>

        <div class="flex gap-4">
            <button id="btn_clear" class="w-1/3 bg-red-500 hover:bg-red-700 text-white font-bold py-3 px-4 rounded">
                Hapus
            </button>
            <button id="btn_kirim" class="w-2/3 bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-4 rounded">
                Kirim Tanda Tangan
            </button>
        </div>
    </div>

    <script>
        const canvas = document.getElementById('signature-pad');
        const layarTunggu = document.getElementById('layar_tunggu');
        const layarCanvas = document.getElementById('layar_canvas');
        
        function resizeCanvas() {
            const ratio =  Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
        }
        window.onresize = resizeCanvas;
        resizeCanvas();

        const signaturePad = new SignaturePad(canvas);

        // --- SISTEM AJAX POLLING (PENGGANTI ECHO) ---
        // Mengecek ke server setiap 1.5 detik
        setInterval(() => {
            axios.get('/cek-status-tablet')
                .then(res => {
                    if (res.data.status === 'buka_canvas' && layarCanvas.classList.contains('hidden')) {
                        // Buka Canvas!
                        layarTunggu.classList.add('hidden');
                        layarCanvas.classList.remove('hidden');
                        signaturePad.clear();
                        resizeCanvas();
                    } 
                    else if (res.data.status === 'menunggu' && layarTunggu.classList.contains('hidden')) {
                        // Kembali ke layar tunggu
                        layarCanvas.classList.add('hidden');
                        layarTunggu.classList.remove('hidden');
                    }
                })
                .catch(err => console.error("Proses cek error: ", err));
        }, 1500);

        // Tombol Hapus Canvas
        document.getElementById('btn_clear').addEventListener('click', () => {
            signaturePad.clear();
        });

        // Tombol Kirim Tanda Tangan
        document.getElementById('btn_kirim').addEventListener('click', () => {
            if (signaturePad.isEmpty()) {
                alert("Tanda tangan tidak boleh kosong!");
                return;
            }

            const dataTtd = signaturePad.toDataURL('image/png');
            
            // Kirim ke server
            axios.post('/terima-ttd', { tanda_tangan: dataTtd })
                .then(res => {
                    // Sembunyikan canvas, kembali ke layar tunggu
                    layarCanvas.classList.add('hidden');
                    layarTunggu.classList.remove('hidden');
                    signaturePad.clear();
                })
                .catch(err => {
                    alert("Gagal kirim data ke server.");
                    console.error(err);
                });
        });
    </script>
</body>
</html>