<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisJasaAkun extends Model
{
    use HasFactory;
    protected $table = 'jenis_jasa_akuns';

    protected $fillable = ['user_id', 'kelas_tarif', 'jenis_jasa','kategori_pendapatans_id'];

    
    public function jenisJasaAkun()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function kategoriPendapatan()
    {
        return $this->belongsTo(KategoriPendapatan::class, 'kategori_pendapatans_id');
    }


}
