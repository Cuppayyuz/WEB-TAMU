<?php

namespace App\Http\Controllers;

use App\Models\web_tamu;
use App\Models\WebTamu;
use Illuminate\Http\Request;

class web_tamu_Controlllers extends Controller
{
    // ==========================================
    // AREA PC PETUGAS
    // ==========================================

    // 1. Halaman Awal Petugas (Tombol untuk mulai)
    public function indexPetugas()
    {
        return view('tamu.index_petugas');
    }

    // 2. Petugas klik "Tamu Baru", kita buat Draft Sesi
    public function buatSesi()
    {
        $tamu = web_tamu::create([
            'status' => 'draft',
            'nama' => '', // Memberikan string kosong agar MySQL tidak protes
            'jenis_tamu' => null, // Tergantung struktur database-mu, mungkin butuh default value
        ]);
        return redirect()->route('tamu.form', $tamu->id);
    }

    // 3. Form Isian Petugas
    public function formPetugas($id)
    {
        $tamu = web_tamu::findOrFail($id);
        return view('tamu.petugas_form', compact('tamu'));
    }

    // 4. API untuk PC mengecek apakah Tamu sudah TTD di tablet
    public function cekTtdPetugas($id)
    {
        $tamu = web_tamu::findOrFail($id);
        return response()->json([
            'sudah_ttd' => $tamu->tanda_tangan !== null ? true : false
        ]);
    }

    // 5. Simpan Data Final & Cetak Struk
    public function simpanFinal(Request $request, $id)
    {
        $tamu = web_tamu::findOrFail($id);

        $tamu->update([
            'jenis_tamu' => $request->jenis_tamu,
            'nama' => $request->nama,
            'mapel' => $request->mapel,
            'kelas' => $request->kelas,
            'jurusan' => $request->jurusan,
            'status' => 'selesai' // Sesi selesai
        ]);

        return redirect()->route('tamu.struk', $id);
    }

    // ==========================================
    // AREA TABLET TAMU
    // ==========================================

    // 1. Halaman Tablet
    public function tablet()
    {
        return view('tamu.tablet');
    }

    // 2. API Tablet mengecek apakah ada sesi 'draft' yang sedang aktif
    public function cekSesiTablet()
    {
        $tamu = web_tamu::where('status', 'draft')->latest()->first();
        return response()->json($tamu);
    }

    // 3. Tablet mengirim TTD ke database
    public function simpanTtdTablet(Request $request, $id)
    {
        $tamu = web_tamu::findOrFail($id);
        $tamu->update(['tanda_tangan' => $request->tanda_tangan]);
        return response()->json(['message' => 'TTD Sukses']);
    }

    // Halaman Struk
    public function struk($id)
    {
        $tamu = web_tamu::findOrFail($id);
        return view('tamu.struk', compact('tamu'));
    }
}
