<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tamus', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_tamu', ['guru', 'murid']);
            $table->string('nama');
            $table->string('mapel')->nullable();
            $table->string('kelas_jurusan')->nullable();
            $table->longText('tanda_tangan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tamus');
    }
};