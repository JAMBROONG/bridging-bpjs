<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPendapatan extends Model
{
    use HasFactory;
    protected $table = 'kategori_pendapatans';
    protected $fillable = ['kategori'];
}
