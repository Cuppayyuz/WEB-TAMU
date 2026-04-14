<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BukuTamuExport implements FromArray, WithHeadings, WithStyles
{
    private $tamus;

    public function __construct($tamus)
    {
        $this->tamus = $tamus;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'Jenis Tamu',
            'Mapel/Kelas/Jurusan',
            'Tanggal',
            'Jam',
            'Status'
        ];
    }

    public function array(): array
    {
        $data = [];
        foreach ($this->tamus as $index => $tamu) {
            $data[] = [
                $index + 1,
                $tamu->nama,
                ucfirst($tamu->jenis_tamu),
                $tamu->jenis_tamu === 'guru' 
                    ? $tamu->mapel 
                    : "{$tamu->kelas} - {$tamu->jurusan}",
                $tamu->created_at->format('d/m/Y'),
                $tamu->created_at->format('H:i:s'),
                ucfirst($tamu->status)
            ];
        }
        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4B5563']],
            ],
        ];
    }
}