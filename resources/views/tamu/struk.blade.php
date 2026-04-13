<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Snack - {{ $tamu->nama }}</title>
    <style>
        /* Ukuran standar Thermal Printer 58mm */
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 58mm; 
            margin: 0 auto;
            padding: 0;
            font-size: 12px;
            color: #000;
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .line { border-bottom: 1px dashed #000; margin: 10px 0; }
        
        /* Hilangkan elemen yang tidak perlu saat diprint */
        @media print {
            @page { margin: 0; }
            body { margin: 10px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    
    <div class="center">
        <h3 style="margin-bottom: 5px;">KUPON SNACK GRATIS</h3>
        <p style="margin: 0;">Event Sekolah Kita</p>
    </div>
    
    <div class="line"></div>

    <p><strong>Nama:</strong> {{ $tamu->nama }}</p>
    <p><strong>Status:</strong> {{ strtoupper($tamu->jenis_tamu) }}</p>
    
    @if($tamu->jenis_tamu == 'guru' && $tamu->mapel)
        <p><strong>Mapel:</strong> {{ $tamu->mapel }}</p>
    @elseif($tamu->jenis_tamu == 'siswa')
        <p><strong>Kelas:</strong> {{ $tamu->kelas }} {{ $tamu->jurusan }}</p>
    @endif

    <div class="line"></div>

    <div class="center">
        <p style="margin-bottom: 5px;">Tanda Tangan:</p>
        <img src="{{ $tamu->tanda_tangan }}" style="max-width: 100%; height: auto;" alt="TTD">
    </div>

    <div class="line"></div>
    <div class="center">
        <p>Tukarkan struk ini di meja konsumsi.</p>
        <p>Terima Kasih!</p>
    </div>

    <button class="no-print" onclick="window.location.href='{{ route('tamu.create') }}'" style="margin-top:20px; width:100%; padding: 10px;">
        Kembali ke Form Tamu
    </button>
</body>
</html>