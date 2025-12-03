<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aturan extends Model
{
    protected $table = 'aturan';

    protected $fillable = [
        'kode_aturan',
        'penyakit_id',
        'gejala_ids',
        'confidence_rule',
    ];

    /**
     * Relasi ke penyakit yang menjadi kesimpulan aturan.
     */
    public function penyakit()
    {
        return $this->belongsTo(PenyakitGigi::class, 'penyakit_id');
    }

    /**
     * Mengembalikan daftar ID gejala sebagai array dari kolom gejala_ids.
     */
    public function getGejalaIdListAttribute(): array
    {
        $value = $this->gejala_ids;

        // Coba decode sebagai JSON terlebih dahulu.
        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // Fallback: string dengan pemisah koma.
        return array_values(array_filter(array_map('trim', explode(',', (string) $value))));
    }
}
