<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use illuminate\View\View;
use App\Models\web_tamu;

class web_tamu_Controlllers extends Controller
{
    public function index(): View
    {
        $data_tamus = web_tamu::latest()->paginate(10);
        return view('web_tamu.index', compact('data_tamus'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'jenis_tamu' => 'required|in:guru,siswa',
            'nama' => 'required|string|max:255',
            'mapel' => 'nullable|string|max:255',
            'kelas' => 'nullable|string|max:255',
            'jurusan' => 'nullable|string|max:255',
            'tanda_tangan' => 'required|string', // Asumsikan tanda tangan dikirim sebagai string (misal Base64)
        ]);

        // Simpan data ke database
        web_tamu::create($validatedData);

        // Redirect kembali ke halaman utama dengan pesan sukses
        return redirect()->route('index')->with('success', 'Data tamu berhasil disimpan!');
    }
}
