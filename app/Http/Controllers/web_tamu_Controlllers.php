<?php

namespace App\Http\Controllers;

use App\Models\web_tamu;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BukuTamuExport;
use Carbon\Carbon;

class web_tamu_Controlllers extends Controller
{
    // ==========================================
    // BUKU TAMU LIST & CRUD
    // ==========================================

    /**
     * List semua tamu dengan search & filter
     */
    public function indexBuku(Request $request)
    {
        $query = web_tamu::query();

        // Search by nama
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        // Filter by jenis tamu
        if ($request->filled('jenis_tamu') && $request->jenis_tamu !== 'semua') {
            $query->where('jenis_tamu', $request->jenis_tamu);
        }

        // Filter by date range
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->date_from)->startOfDay(),
                Carbon::parse($request->date_to)->endOfDay()
            ]);
        }

        // Order by latest & paginate
        $tamus = $query->orderBy('id', 'desc')->paginate(15);

        return view('tamu.buku_list', compact('tamus'));
    }

    /**
     * Detail view tamu + signature
     */
    public function showBuku($id)
    {
        $tamu = web_tamu::findOrFail($id);
        return view('tamu.buku_detail', compact('tamu'));
    }

    /**
     * Edit form
     */
    public function editBuku($id)
    {
        $tamu = web_tamu::findOrFail($id);
        return view('tamu.buku_edit', compact('tamu'));
    }

    /**
     * Update data tamu
     */
    public function updateBuku(Request $request, $id)
    {
        try {
            $tamu = web_tamu::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
                'jenis_tamu' => 'required|in:guru,siswa',
                'mapel' => 'nullable|string|max:255',
                'kelas' => 'nullable|string|max:255',
                'jurusan' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $tamu->update([
                'nama' => $request->nama,
                'jenis_tamu' => $request->jenis_tamu,
                'mapel' => $request->mapel,
                'kelas' => $request->kelas,
                'jurusan' => $request->jurusan,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete tamu
     */
    public function destroyBuku($id)
    {
        try {
            $tamu = web_tamu::findOrFail($id);
            $tamu->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==========================================
    // EXPORT FUNCTIONS
    // ==========================================

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        $query = web_tamu::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->date_from)->startOfDay(),
                Carbon::parse($request->date_to)->endOfDay()
            ]);
        }

        $tamus = $query->orderBy('id', 'desc')->get();

        return Excel::download(
            new BukuTamuExport($tamus),
            'buku_tamu_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    /**
     * Export to PDF
     */
    public function exportPdf(Request $request)
    {
        $query = web_tamu::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->date_from)->startOfDay(),
                Carbon::parse($request->date_to)->endOfDay()
            ]);
        }

        $tamus = $query->orderBy('id', 'desc')->get();

        $pdf = Pdf::loadView('tamu.buku_pdf', compact('tamus'));
        return $pdf->download('buku_tamu_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    // ==========================================
    // ORIGINAL FUNCTIONS (TABLET & PETUGAS FORM)
    // ==========================================

    public function indexPetugas()
    {
        return view('tamu.index_petugas');
    }

    public function buatSesi()
    {
        $tamu = web_tamu::create([
            'status' => 'draft',
            'nama' => '',
            'jenis_tamu' => null,
        ]);
        return redirect()->route('tamu.form', $tamu->id);
    }

    public function formPetugas($id)
    {
        $tamu = web_tamu::findOrFail($id);
        return view('tamu.petugas_form', compact('tamu'));
    }

    public function tablet()
    {
        return view('tamu.tablet');
    }

    public function cekSesiTablet()
    {
        $tamu = web_tamu::where('status', 'draft')->latest()->first();
        return response()->json($tamu);
    }

    public function cekTtdPetugas($id)
    {
        $tamu = web_tamu::findOrFail($id);
        return response()->json([
            'sudah_ttd' => $tamu->tanda_tangan !== null ? true : false
        ]);
    }

    /**
     * Simpan Final Data + Signature (AJAX)
     */
    public function simpanFinal(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
                'jenis_tamu' => 'required|in:guru,siswa',
                'mapel' => 'nullable|string|max:255',
                'kelas' => 'nullable|string|max:255',
                'jurusan' => 'nullable|string|max:255',
                'signature' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $tamu = web_tamu::findOrFail($id);

            // Decode Base64 & Simpan Signature
            $signatureBase64 = $request->signature;
            $signaturePath = null;

            if (strpos($signatureBase64, 'data:image') === 0) {
                $imageData = explode(',', $signatureBase64)[1];
                $imageBinary = base64_decode($imageData);

                $filename = 'signature_' . $tamu->id . '_' . time() . '.png';
                $path = Storage::disk('public')->put('signatures/' . $filename, $imageBinary);

                $signaturePath = 'signatures/' . $filename;
            }

            // Update record
            $tamu->update([
                'nama' => $request->nama,
                'jenis_tamu' => $request->jenis_tamu,
                'mapel' => $request->mapel,
                'kelas' => $request->kelas,
                'jurusan' => $request->jurusan,
                'tanda_tangan' => $signaturePath,
                'status' => 'selesai'
            ]);

            // Trigger printer
            $this->triggerPrinter($tamu);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'data' => [
                    'id' => $tamu->id,
                    'redirect_url' => route('tamu.struk', $tamu->id)
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function struk($id)
    {
        $tamu = web_tamu::findOrFail($id);
        return view('tamu.struk', compact('tamu'));
    }

    private function triggerPrinter($tamu)
    {
        /**
         * ================================
         * PRINTER TRIGGER - CONSOLE LOG
         * ================================
         * Untuk sekarang: console.log di browser
         * Nanti bisa di-upgrade ke:
         * - Epson TM-series thermal printer
         * - Menggunakan library: mike42/escpos-php
         * - Atau via network printing
         */

        $printData = [
            'id' => $tamu->id,
            'nama' => $tamu->nama,
            'jenis_tamu' => $tamu->jenis_tamu,
            'mapel' => $tamu->mapel,
            'kelas' => $tamu->kelas,
            'jurusan' => $tamu->jurusan,
            'waktu' => now()->format('d/m/Y H:i:s'),
            'signature_path' => $tamu->tanda_tangan,
            'timestamp' => now()
        ];

        // Log untuk debugging
        \Log::info('🖨️ PRINTER TRIGGER', $printData);

        // FUTURE: Uncomment dan configure untuk Epson printer
        /*
        try {
            $printer = new Escpos();
            $printer->setFont(Escpos::FONT_A);
            $printer->text("==== STRUK TAMU ====\n");
            $printer->text("Nama: " . $tamu->nama . "\n");
            $printer->text("Jenis: " . $tamu->jenis_tamu . "\n");
            $printer->text("Waktu: " . now()->format('d/m/Y H:i:s') . "\n");
            $printer->text("====================\n");
            $printer->cut();
            
            \Log::info('✅ Print sent to Epson printer');
        } catch (\Exception $e) {
            \Log::error('❌ Printer error: ' . $e->getMessage());
        }
        */
    }
    /**
     * Simpan TTD dari tablet & broadcast ke laptop
     */
    public function simpanTtdTablet(Request $request, $id)
    {
        try {
            $tamu = web_tamu::findOrFail($id);

            $signatureBase64 = $request->signature;
            $signaturePath = null;

            // Decode & save signature
            if (strpos($signatureBase64, 'data:image') === 0) {
                $imageData = explode(',', $signatureBase64)[1];
                $imageBinary = base64_decode($imageData);

                $filename = 'signature_' . $tamu->id . '_' . time() . '.png';
                $path = Storage::disk('public')->put('signatures/' . $filename, $imageBinary);
                $signaturePath = 'signatures/' . $filename;
            }

            // Update database
            $tamu->update([
                'tanda_tangan' => $signaturePath,
            ]);

            \Log::info('✓ TTD saved for tamu ID: ' . $id);

            return response()->json([
                'success' => true,
                'message' => 'TTD Sukses disimpan'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTamuData($id)
    {
        try {
            $tamu = web_tamu::findOrFail($id);
            return response()->json($tamu, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Not found'
            ], 404);
        }
    }
}
