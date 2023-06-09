<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorObat extends Model
{
    use HasFactory;
    protected $table = 'vendor_obats';

    protected $fillable = ['vendor','user_id'];
    
    public function vendorObat()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
