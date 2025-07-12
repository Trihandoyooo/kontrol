<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outcome extends Model
{
    use HasFactory;

    protected $fillable = [
    'nik', 'judul', 'nama_kegiatan', 'keterangan', 'dapil',
    'manfaat', 'dokumentasi', 'tanggal',
    'status', 'alasan_tolak'
];


    protected $casts = [
        'dokumentasi' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'nik', 'nik');
    }
}
