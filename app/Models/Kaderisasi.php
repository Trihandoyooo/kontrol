<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Kaderisasi extends Model
{
    protected $table = 'kaderisasi';

    protected $fillable = [
        'nik', 'judul', 'tanggal', 'dokumentasi', 'peserta', 'catatan', 'status', 'alasan_tolak'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'nik', 'nik');
    }

    // Accessor supaya bisa panggil $model->dokumentasi_array
    public function getDokumentasiArrayAttribute()
    {
        $files = json_decode($this->dokumentasi);
        return is_array($files) ? $files : [];
    }
}
