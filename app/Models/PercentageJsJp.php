<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PercentageJsJp extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'percentage_js_jp';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'js',
        'jp'
    ];

    /**
     * Get the user that owns the data pendapatan rs ris.
     */
    public function percentageJsJp()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
