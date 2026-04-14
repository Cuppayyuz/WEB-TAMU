<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class web_tamu extends Model
{
    protected $table = 'web_tamus';

    protected $fillable = [
        'nama',
        'jenis_tamu',
        'mapel',
        'kelas',
        'jurusan',
        'tanda_tangan',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    // Accessor untuk nomor urut
    public function getNomorAttribute()
    {
        return $this->id;
    }

    // Format tanggal
    public function getTanggalFormatAttribute()
    {
        return $this->created_at->format('d/m/Y');
    }

    // Format jam
    public function getJamFormatAttribute()
    {
        return $this->created_at->format('H:i:s');
    }

    // Status badge
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'draft' => '<span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">Draft</span>',
            'selesai' => '<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Selesai</span>',
            default => '<span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">Unknown</span>'
        };
    }
}