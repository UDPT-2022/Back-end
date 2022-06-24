<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    use HasFactory;

    protected $primaryKey = 'MA_SP';
    protected $fillable = [
        'TEN_SP',
        'MA_CUA_HANG',
        'GIA_SP',
        'SL_CON_LAI',
        'MO_TA',
        'DANH_GIA',
        'ANH'
    ];
}
