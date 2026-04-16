<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TamuController;

Route::get('/', function () {
    return redirect('/petugas_tamu');
});

Route::get('/petugas_tamu', [TamuController::class, 'petugas']);
Route::post('/petugas_tamu', [TamuController::class, 'store']);
Route::delete('/petugas_tamu/{id}', [TamuController::class, 'destroy']);

Route::get('/tanda_tangan', [TamuController::class, 'tablet']);

// Route API untuk komunikasi antar device
Route::post('/trigger-canvas', [TamuController::class, 'triggerCanvas']);
Route::post('/terima-ttd', [TamuController::class, 'terimaTtd']);

Route::post('/trigger-canvas', [TamuController::class, 'triggerCanvas']);
Route::get('/cek-status-tablet', [TamuController::class, 'cekStatusTablet']);
Route::post('/terima-ttd', [TamuController::class, 'terimaTtd']);
Route::get('/cek-ttd-laptop', [TamuController::class, 'cekTtdLaptop']);

Route::get('/export-tamu', [TamuController::class, 'export']);