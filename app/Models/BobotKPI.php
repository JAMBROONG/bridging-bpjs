<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BobotKPI extends Model
{
    use HasFactory;
    
    protected $table = 'bobot_k_p_i_s';

    
    protected $fillable = [
        'user_id',
        'kategori_id',
        'bobot'
    ];

    /**
     * Get the user that owns the data pendapatan rs ris.
     */
    public function dataKPI()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function dataKategori()
    {
        return $this->belongsTo(KpiKategori::class, 'kategori_id');
    }

}
