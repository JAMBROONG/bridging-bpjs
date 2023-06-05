<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateKelasTarif extends Model
{
    use HasFactory;
    
    
    protected $table = 'template_kelas_tarifs';

    protected $fillable = ['kelas_tarif','template', 'kategori_pendapatans_id','jenis_jasa'];
    
    public function kategoriPendapatan()
    {
        return $this->belongsTo(KategoriPendapatan::class, 'kategori_pendapatans_id');
    }

}
