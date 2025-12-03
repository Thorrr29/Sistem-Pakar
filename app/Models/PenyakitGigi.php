<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenyakitGigi extends Model
{
    /**
     * Nama tabel eksplisit karena tidak mengikuti konvensi jamak Laravel.
     */
    protected $table = 'penyakit_gigi';

    /**
     * Kolom yang boleh diisi secara mass-assignment.
     */
    protected $fillable = [
        'kode_penyakit',
        'nama_penyakit',
        'deskripsi',
        'saran_penanganan',
    ];

    /**
     * Relasi ke aturan yang terkait dengan penyakit ini.
     */
    public function aturan()
    {
        return $this->hasMany(Aturan::class, 'penyakit_id');
    }

    /**
     * Relasi ke riwayat konsultasi yang menghasilkan penyakit ini.
     */
    public function konsultasi()
    {
        return $this->hasMany(Konsultasi::class, 'hasil_penyakit_id');
    }
}
