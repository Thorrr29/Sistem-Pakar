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
        Schema::create('aturan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_aturan')->unique();
            $table->foreignId('penyakit_id')
                ->constrained('penyakit_gigi')
                ->onDelete('cascade');
            // Menyimpan daftar ID gejala dalam bentuk JSON (array of IDs) atau string dipisah koma.
            $table->text('gejala_ids');
            // Nilai bobot / confidence dari aturan ini terhadap penyakit terkait.
            $table->float('confidence_rule')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aturan');
    }
};
