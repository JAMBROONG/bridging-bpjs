<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPendapatanRsRi extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'data_pendapatan_rs_ris';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rm',
        'notrans',
        'tanggal',
        'pasien',
        'unit',
        'faktur',
        'produk',
        'obat',
        'qty',
        'tarip',
        'jumlah',
        'dokter',
        'penjamin',
        'invoice',
        'bayar',
        'jenis_layanan',
        'kategori_layanan',
        'klasifikasi',
        'users_id',
    ];

    /**
     * Get the user that owns the data pendapatan rs ris.
     */
    public function dataPendapatanRsRi()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
