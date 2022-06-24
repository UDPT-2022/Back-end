<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    use HasFactory;
    protected $primaryKey = 'MA_DON_HANG';
    protected $fillable = [
        'MA_NGUOI_DUNG',
        'MA_CUA_HANG',
        'MA_SHIPPER',
        'DIA_CHI',
        'TRANG_THAI'
    ];
}
