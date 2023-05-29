<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiDokter extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kpi_dokters';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'kelompok',
        'nilai',
        'bobot',
        'user_id',
    ];

    /**
     * Get the user that owns the data pendapatan rs ris.
     */
    public function kpiDokter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
