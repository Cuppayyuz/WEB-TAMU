<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('web_tamus', function (Blueprint $table) {
            $table->id();

            // Kolom Wajib
            $table->enum('jenis_tamu', ['guru', 'siswa']);
            $table->string('nama');

            // Kolom Spesifik (Dibuat nullable agar tidak error saat disubmit)
            $table->string('mapel')->nullable();
            $table->string('kelas')->nullable();
            $table->string('jurusan')->nullable();

            // Kolom Tanda Tangan
            // Menggunakan text() karena jika kamu menyimpan ttd dalam format Base64, stringnya akan sangat panjang.
            // Jika kamu menyimpannya sebagai file gambar (.png) di storage, string() biasa sudah cukup.
            $table->text('tanda_tangan');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_tamus');
    }
};
