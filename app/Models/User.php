<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Rapat;
use App\Models\Iuran;
use App\Models\Kaderisasi;
use App\Models\Outcome;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $primaryKey = 'nik';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nik', 'name', 'email', 'password', 'role',
        // Data pribadi
        'nama_lengkap', 'gelar_depan', 'gelar_belakang',
        'nomor_kta', 'foto_ktp', 'foto_kta','dapil',
        'tempat_lahir', 'tanggal_lahir', 'usia',
        'jenis_kelamin', 'agama', 'status_perkawinan','jumlah_suara',
        'jumlah_suara_sebelumnya',

        // Alamat
        'alamat_ktp', 'rt', 'rw', 'kecamatan', 'kelurahan', 'kabupaten',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relasi
    public function rapats()
    {
        return $this->hasMany(Rapat::class, 'nik', 'nik');
    }

    public function outcomes()
{
    return $this->hasMany(Outcome::class, 'nik', 'nik');
}

public function iurans()
{
    return $this->hasMany(Iuran::class, 'nik', 'nik');
}

public function kaderisasis()
{
    return $this->hasMany(Kaderisasi::class, 'nik', 'nik');
}

}
