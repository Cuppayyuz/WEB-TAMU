<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Buku Tamu</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            color: #000;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 12px;
            color: #666;
        }
        .date-range {
            text-align: right;
            font-size: 11px;
            color: #666;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table thead {
            background-color: #333;
            color: white;
        }
        table th {
            padding: 10px;
            text-align: left;
            font-size: 12px;
            font-weight: bold;
        }
        table td {
            padding: 8px 10px;
            font-size: 11px;
            border-bottom: 1px solid #ddd;
        }
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-draft {
            background-color: #fff3cd;
            padding: 3px 6px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 10px;
        }
        .status-selesai {
            background-color: #d4edda;
            padding: 3px 6px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 10px;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

<div class="container">
    
    <!-- Header -->
    <div class="header">
        <h1>📖 LAPORAN BUKU TAMU</h1>
        <p>Dicetak: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">Nama</th>
                <th style="width: 10%;">Jenis</th>
                <th style="width: 25%;">Detail</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 10%;">Jam</th>
                <th style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tamus as $index => $tamu)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $tamu->nama }}</strong></td>
                <td>{{ ucfirst($tamu->jenis_tamu) }}</td>
                <td>
                    @if($tamu->jenis_tamu === 'guru')
                        {{ $tamu->mapel ?? '-' }}
                    @else
                        {{ $tamu->kelas ?? '-' }} - {{ $tamu->jurusan ?? '-' }}
                    @endif
                </td>
                <td>{{ $tamu->created_at->format('d/m/Y') }}</td>
                <td>{{ $tamu->created_at->format('H:i:s') }}</td>
                <td>
                    <span class="status-{{ $tamu->status }}">{{ ucfirst($tamu->status) }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px;">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Total Tamu: <strong>{{ count($tamus) }}</strong></p>
        <p style="margin-top: 20px;">Laporan ini dibuat secara otomatis oleh sistem</p>
    </div>

</div>

</body>
</html>