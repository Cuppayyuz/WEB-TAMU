<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\web_tamu_Controlllers;

Route::get('/', [web_tamu_Controlllers::class, 'indexPetugas'])->name('tamu.index');
Route::post('/buat-sesi', [web_tamu_Controlllers::class, 'buatSesi'])->name('tamu.buatSesi');
Route::get('/form/{id}', [web_tamu_Controlllers::class, 'formPetugas'])->name('tamu.form');
Route::get('/api/cek-ttd-petugas/{id}', [web_tamu_Controlllers::class, 'cekTtdPetugas']);
Route::post('/simpan-final/{id}', [web_tamu_Controlllers::class, 'simpanFinal'])->name('tamu.simpanFinal');

// Route Tablet Tamu
Route::get('/tablet', [web_tamu_Controlllers::class, 'tablet'])->name('tamu.tablet');
Route::get('/api/cek-sesi-tablet', [web_tamu_Controlllers::class, 'cekSesiTablet']);
Route::post('/api/simpan-ttd-tablet/{id}', [web_tamu_Controlllers::class, 'simpanTtdTablet']);

// Route Struk
Route::get('/struk/{id}', [web_tamu_Controlllers::class, 'struk'])->name('tamu.struk');