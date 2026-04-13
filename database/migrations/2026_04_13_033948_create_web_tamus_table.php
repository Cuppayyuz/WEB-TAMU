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

            // Status sesi saat ini
            $table->enum('status', ['draft', 'selesai'])->default('draft');

            // Kolom Wajib (Sekarang nullable karena diisi menyusul oleh petugas)
            $table->enum('jenis_tamu', ['guru', 'siswa'])->nullable();
            $table->string('nama')->nullable(); // <-- INI YANG BIKIN ERROR, PASTIKAN ADA ->nullable()
    
            // Kolom Spesifik
            $table->string('mapel')->nullable();
            $table->string('kelas')->nullable();
            $table->string('jurusan')->nullable();

            // Kolom Tanda Tangan
            $table->text('tanda_tangan')->nullable();

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
