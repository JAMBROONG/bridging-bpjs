<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subscribes_id',
        'invoice_number',
        'amount',
        'payment_status',
        'payment_date',
        'payment_method',
    ];

    /**
     * Get the user that owns the data pendapatan rs ris.
     */
    public function invoices()
    {
        return $this->belongsTo(Subscribe::class, 'subscribes_id');
    }
}
