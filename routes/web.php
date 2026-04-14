<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\web_tamu_Controlllers;

// ==========================================
// ROUTES PETUGAS (SUDAH ADA - JANGAN HAPUS)
// ==========================================
Route::get('/', [web_tamu_Controlllers::class, 'indexPetugas'])->name('tamu.index');
Route::post('/buat-sesi', [web_tamu_Controlllers::class, 'buatSesi'])->name('tamu.buatSesi');
Route::get('/form/{id}', [web_tamu_Controlllers::class, 'formPetugas'])->name('tamu.form');
Route::get('/api/cek-ttd-petugas/{id}', [web_tamu_Controlllers::class, 'cekTtdPetugas']);
Route::post('/simpan-final/{id}', [web_tamu_Controlllers::class, 'simpanFinal'])->name('tamu.simpanFinal');

// ==========================================
// ROUTES TABLET TAMU (SUDAH ADA - JANGAN HAPUS)
// ==========================================
Route::get('/tablet', [web_tamu_Controlllers::class, 'tablet'])->name('tamu.tablet');
Route::get('/api/cek-sesi-tablet', [web_tamu_Controlllers::class, 'cekSesiTablet']);
Route::post('/api/simpan-ttd-tablet/{id}', [web_tamu_Controlllers::class, 'simpanTtdTablet']);
Route::get('/api/tamu/{id}', [web_tamu_Controlllers::class, 'getTamuData'])->name('api.tamu');
// ==========================================
// ROUTES STRUK (SUDAH ADA - JANGAN HAPUS)
// ==========================================
Route::get('/struk/{id}', [web_tamu_Controlllers::class, 'struk'])->name('tamu.struk');

// ==========================================
// BUKU TAMU ROUTES - CRUD & EXPORT (TAMBAHAN BARU)
// ==========================================
Route::get('/tamu/buku', [web_tamu_Controlllers::class, 'indexBuku'])->name('tamu.buku.index');
Route::get('/tamu/buku/{id}', [web_tamu_Controlllers::class, 'showBuku'])->name('tamu.buku.show');
Route::get('/tamu/buku/{id}/edit', [web_tamu_Controlllers::class, 'editBuku'])->name('tamu.buku.edit');
Route::post('/tamu/buku/{id}/update', [web_tamu_Controlllers::class, 'updateBuku'])->name('tamu.buku.update');
Route::delete('/tamu/buku/{id}', [web_tamu_Controlllers::class, 'destroyBuku'])->name('tamu.buku.destroy');
Route::get('/tamu/buku/export/excel', [web_tamu_Controlllers::class, 'exportExcel'])->name('tamu.buku.export.excel');
Route::get('/tamu/buku/export/pdf', [web_tamu_Controlllers::class, 'exportPdf'])->name('tamu.buku.export.pdf');