<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscribe extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subscribe';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'payment_status',
    ];

    /**
     * Get the user that owns the data pendapatan rs ris.
     */
    public function subscribe()
    {   
        return $this->belongsTo(User::class, 'users_id');
    }
}
