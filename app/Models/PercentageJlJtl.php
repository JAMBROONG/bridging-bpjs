<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PercentageJlJtl extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'percentage_jl_jtl';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'jl',
        'jtl'
    ];

    /**
     * Get the user that owns the data pendapatan rs ris.
     */
    public function percentageJlJtl()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
