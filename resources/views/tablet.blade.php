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
        body {
            touch-action: none;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }

        .dot-blink {
            animation: blink 1.5s ease-in-out infinite;
        }
    </style>
</head>

<body class="bg-slate-100 h-screen flex items-center justify-center p-4"
    style="background-image: url('{{ asset('images/back.jpg') }}'); background-size: cover;">

    <div id="layar_tunggu" class="max-w-xl text-center scale-105 bg-white/10 backdrop-blur-md rounded-2xl p-10 shadow-lg">
        <div class="flex flex-col items-center gap-6">

            <div class="w-20 h-20 rounded-full border-2 border-blue-400 bg-blue-50 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2a10 10 0 1 0 0 20A10 10 0 0 0 12 2zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6zm0 13a7.96 7.96 0 0 1-5.6-2.3c.4-1.7 2.1-3 3.6-3h4c1.5 0 3.2 1.3 3.6 3A7.96 7.96 0 0 1 12 20z" />
                </svg>
            </div>

            <div>
                <h1 class="text-3xl font-medium text-slate-800 mb-2">Selamat Datang!</h1>
                <p class="text-slate-400 text-base">Mohon tunggu instruksi dari petugas</p>
            </div>

            <div class="flex items-center gap-2 mt-1">
                <div class="w-2 h-2 rounded-full bg-blue-500 dot-blink"></div>
                <span class="text-blue-500 text-sm font-medium">Menunggu</span>
            </div>

        </div>
    </div>
    <div id="layar_canvas" class=" hidden w-full max-w-xl bg-slate-50 p-6 rounded-2xl shadow-xl">

        <div class="flex items-center gap-3 mb-5">
            <div class="w-9 h-9 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                </svg>
            </div>
            <div>
                <p class="text-slate-800 text-base font-medium leading-none mb-0.5">Tanda Tangan</p>
                <p class="text-slate-400 text-xs">Tanda tangani di area bawah</p>
            </div>
        </div>

        {{-- Area Canvas --}}
        <div class="bg-white border-2 border-dashed border-slate-200 rounded-xl mb-4 overflow-hidden">
            <canvas id="signature-pad" class="w-full h-52 block"></canvas>
        </div>

        {{-- Tombol --}}
        <div class="flex gap-3">
            <button id="btn_clear"
                class="bg-white border border-red-200 text-red-500 hover:bg-red-50 font-medium py-2.5 px-5 rounded-lg text-sm transition-colors">
                Hapus
            </button>
            <button id="btn_kirim"
                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-5 rounded-lg text-sm transition-colors flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="white" viewBox="0 0 24 24">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" />
                </svg>
                Kirim Tanda Tangan
            </button>
        </div>

    </div>

    <script>
        const canvas = document.getElementById('signature-pad');
        const layarTunggu = document.getElementById('layar_tunggu');
        const layarCanvas = document.getElementById('layar_canvas');

        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
        }
        window.onresize = resizeCanvas;
        resizeCanvas();

        const signaturePad = new SignaturePad(canvas);

        setInterval(() => {
            axios.get('/cek-status-tablet')
                .then(res => {
                    if (res.data.status === 'buka_canvas' && layarCanvas.classList.contains('hidden')) {
                        layarTunggu.classList.add('hidden');
                        layarCanvas.classList.remove('hidden');
                        signaturePad.clear();
                        resizeCanvas();
                    } else if (res.data.status === 'menunggu' && layarTunggu.classList.contains('hidden')) {
                        layarCanvas.classList.add('hidden');
                        layarTunggu.classList.remove('hidden');
                    }
                })
                .catch(err => console.error("Proses cek error: ", err));
        }, 1500);

        document.getElementById('btn_clear').addEventListener('click', () => {
            signaturePad.clear();
        });

        document.getElementById('btn_kirim').addEventListener('click', () => {
            if (signaturePad.isEmpty()) {
                alert("Tanda tangan tidak boleh kosong!");
                return;
            }

            const dataTtd = signaturePad.toDataURL('image/png');

            axios.post('/terima-ttd', {
                    tanda_tangan: dataTtd
                })
                .then(res => {
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