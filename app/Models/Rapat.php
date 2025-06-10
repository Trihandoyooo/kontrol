<?php

// app/Models/Rapat.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rapat extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'jenis_rapat',
        'judul',
        'notulen',
        'peserta',
        'lokasi',
        'tanggal',
        'dokumentasi',
        'status',
        'alasan_tolak',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'nik', 'nik');
    }
}
