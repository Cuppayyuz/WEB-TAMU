<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class web_tamu extends Model
{
    protected $fillable = [
        'jenis_tamu',
        'nama',
        'mapel',
        'kelas',
        'jurusan',
        'tanda_tangan',
    ];
}
