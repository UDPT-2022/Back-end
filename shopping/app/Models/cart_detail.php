<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cart_detail extends Model
{
    use HasFactory;
    protected $fillable = [
        'MA_GIO_HANG',
        'MA_SP',
        'MA_DON_HANG',
        'SO_LUONG',
        'DON_GIA',
        'GIA'
    ];
}
