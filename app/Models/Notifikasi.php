<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi'; // ✅ Nama tabel eksplisit

    protected $fillable = [
        'nik',
        'judul',
        'tipe',
        'pesan',
        'peringatan_ke',
        'dibaca',
        'dibaca_pada'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'nik', 'nik');
    }
}
