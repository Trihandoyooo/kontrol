<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlokasiDana extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kegiatan',
        'jumlah',
        'tanggal',
        'deskripsi',
        'dokumentasi',
    ];

    protected $casts = [
        'dokumentasi' => 'array',
        'tanggal' => 'date',
    ];
}
