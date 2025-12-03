<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konsultasi extends Model
{
    protected $table = 'konsultasi';

    protected $fillable = [
        'user_name',
        'user_email',
        'gejala_terpilih',
        'hasil_penyakit_id',
        'skor_kepercayaan',
        'catatan_engine',
    ];

    protected $casts = [
        'gejala_terpilih' => 'array',
        'skor_kepercayaan' => 'float',
    ];

    /**
     * Relasi ke penyakit yang menjadi hasil konsultasi.
     */
    public function penyakit()
    {
        return $this->belongsTo(PenyakitGigi::class, 'hasil_penyakit_id');
    }
}
