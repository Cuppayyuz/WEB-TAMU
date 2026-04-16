<?php

namespace App\Http\Controllers;

use App\Models\Tamu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TamuController extends Controller
{
    public function petugas() {
        $tamus = Tamu::latest()->get();
        return view('petugas', compact('tamus'));
    }

    public function tablet() {
        return view('tablet');
    }

    public function triggerCanvas() {
        Cache::put('status_tablet', 'buka_canvas', now()->addMinutes(10));
        Cache::forget('ttd_sementara');
        return response()->json(['status' => 'sukses']);
    }

    public function cekStatusTablet() {
        $status = Cache::get('status_tablet', 'menunggu');
        return response()->json(['status' => $status]);
    }

    public function terimaTtd(Request $request) {
        Cache::put('ttd_sementara', $request->tanda_tangan, now()->addMinutes(10));
        Cache::put('status_tablet', 'menunggu', now()->addMinutes(10));
        return response()->json(['status' => 'sukses']);
    }

    public function cekTtdLaptop() {
        $ttd = Cache::get('ttd_sementara');
        return response()->json(['ttd' => $ttd]);
    }

    public function store(Request $request) {
        $request->validate([
            'jenis_tamu' => 'required',
            'nama' => 'required',
            'tanda_tangan' => 'required'
        ]);

        // Ambil semua request KECUALI token, kelas, dan jurusan
        $data = $request->except(['_token', 'kelas', 'jurusan']);

        // Gabungkan Kelas dan Jurusan jika yang dipilih adalah Murid
        if($request->jenis_tamu == 'murid'){
            $data['kelas_jurusan'] = "Kelas: " . $request->kelas . " - " . $request->jurusan;
        }

        // Simpan data yang sudah bersih ke database
        Tamu::create($data);
        
        Cache::forget('ttd_sementara');
        Cache::put('status_tablet', 'menunggu', now()->addMinutes(10));

        return redirect('/petugas_tamu')->with('success', 'Data Tamu Berhasil Disimpan!');
    }

    // --- FITUR EXPORT CSV (Bisa dibuka di Excel) ---
    public function export() {
        $tamus = Tamu::all();
        $fileName = 'data_tamu_' . date('Y-m-d') . '.csv';
        
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Nama', 'Jenis Tamu', 'Detail (Mapel/Kelas)', 'Waktu');

        $callback = function() use($tamus, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tamus as $tamu) {
                fputcsv($file, array(
                    $tamu->nama, 
                    ucfirst($tamu->jenis_tamu), 
                    $tamu->jenis_tamu == 'guru' ? $tamu->mapel : $tamu->kelas_jurusan,
                    $tamu->created_at
                ));
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destroy($id) {
        Tamu::findOrFail($id)->delete();
        return redirect('/petugas_tamu')->with('success', 'Data Dihapus!');
    }
}