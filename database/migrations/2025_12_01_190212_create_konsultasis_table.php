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
        Schema::create('konsultasi', function (Blueprint $table) {
            $table->id();
            $table->string('user_name');
            $table->string('user_email')->nullable();
            // Menyimpan gejala yang dipilih user dalam bentuk JSON
            // berisi array id atau kode gejala.
            $table->text('gejala_terpilih');
            $table->foreignId('hasil_penyakit_id')
                ->nullable()
                ->constrained('penyakit_gigi')
                ->nullOnDelete();
            $table->float('skor_kepercayaan')->nullable();
            // Catatan tambahan dari engine Python (misalnya detail perhitungan / rule yang cocok).
            $table->text('catatan_engine')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konsultasi');
    }
};
