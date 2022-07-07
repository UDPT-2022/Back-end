<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order_detail extends Model
{
    use HasFactory;
    protected $fillable = [
        'MA_DON_HANG',
        'MA_SP',
        'SO_LUONG',
        'DON_GIA',
        'GIA',
    ];
}
