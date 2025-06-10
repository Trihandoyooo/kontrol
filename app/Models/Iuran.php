<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iuran extends Model
{
    use HasFactory;

    protected $table = 'iurans';

    protected $fillable = [
        'nik',
        'jenis_iuran',
        'nominal',
        'tanggal',
        'catatan',
        'dokumentasi',
        'status',
        'alasan_ditolak',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'nik', 'nik');
    }
}
