<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tablet Tanda Tangan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

    <div id="layar-standby" class="text-center">
        <h1 class="text-4xl font-bold text-gray-400">Silakan Lapor ke Petugas</h1>
    </div>

    <div id="layar-ttd" class="hidden bg-white p-8 rounded-lg shadow-lg w-full max-w-2xl text-center">
        <h2 class="text-3xl font-bold text-gray-800 mb-4">Silakan Tanda Tangan</h2>

        <div class="mb-6 relative border-4 border-dashed border-gray-400 rounded bg-gray-50">
            <canvas id="signature-canvas" class="w-full h-64"></canvas>
            <button id="clear-signature" class="absolute bottom-2 right-2 text-sm text-red-600 bg-red-100 px-2 py-1 rounded">Hapus</button>
        </div>

        <button onclick="kirimTtd()" class="w-full bg-green-500 text-white font-bold py-4 rounded-lg hover:bg-green-600 text-xl shadow-lg">
            Kirim Tanda Tangan
        </button>
    </div>

    <script>
        let currentSesiId = null;
        let isSigning = false;

        const canvas = document.getElementById('signature-canvas');
        const signaturePad = new SignaturePad(canvas);

        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
        }
        document.getElementById('clear-signature').addEventListener('click', () => signaturePad.clear());

        // POLLING: Cek sesi dari PC
        setInterval(async () => {
            if(isSigning) return; // Jika tablet sedang dipakai, jangan poll database

            try {
                let response = await fetch('/api/cek-sesi-tablet');
                let data = await response.json();

                // Jika ada draft, tapi TTD-nya MASIH KOSONG, maka tampilkan kanvas
                if(data && data.id && data.tanda_tangan === null) {
                    currentSesiId = data.id;
                    isSigning = true;
                    
                    document.getElementById('layar-standby').classList.add('hidden');
                    document.getElementById('layar-ttd').classList.remove('hidden');
                    resizeCanvas();
                    signaturePad.clear();
                }
            } catch (error) {}
        }, 1500);

        async function kirimTtd() {
            if (signaturePad.isEmpty()) return alert("Tanda tangan tidak boleh kosong!");

            const base64TTD = signaturePad.toDataURL();

            // Kirim ke server
            await fetch(`/api/simpan-ttd-tablet/${currentSesiId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ tanda_tangan: base64TTD })
            });

            // Sukses, kembalikan tablet ke mode Standby
            document.getElementById('layar-ttd').classList.add('hidden');
            document.getElementById('layar-standby').classList.remove('hidden');
            document.getElementById('layar-standby').innerHTML = '<h1 class="text-4xl font-bold text-green-500">Terima Kasih!</h1>';
            
            // Reset state
            isSigning = false;
            currentSesiId = null;

            setTimeout(() => {
                document.getElementById('layar-standby').innerHTML = '<h1 class="text-4xl font-bold text-gray-400">Silakan Lapor ke Petugas</h1>';
            }, 3000);
        }
    </script>
</body>
</html>