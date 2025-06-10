<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $primaryKey = 'nik';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nik',
        'name',
        'email',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function rapats()
{
    return $this->hasMany(Rapat::class, 'nik', 'nik');
}

 public function kaderisasi()
    {
        return $this->hasMany(Kaderisasi::class, 'nik', 'nik');
        // foreign key di kaderisasi = 'nik'
        // local key di users = 'nik'
    }

}
