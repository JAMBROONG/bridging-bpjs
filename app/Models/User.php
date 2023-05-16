<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'direktur',
        'tanggal_pendaftaran',
        'alamat',
        'telepon',
        'logo',
        'website',
        'kepemilikan',
        'luas_tanah','luas_bangunan',
        'kelas',
        'status_blu',
        'npwp',
        'akte_pendirian',
        'surat_izin_usaha',
        'nomor_registrasi_bpjs',
        'klasifikasi_lapangan_usaha',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the data pendapatan rs ris associated with the user.
     */
    public function dataPendapatanRsRi()
    {
        return $this->hasMany(DataPendapatanRsRi::class, 'users_id');
    }
}
