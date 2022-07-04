<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class profile extends Model
{
    use HasFactory;
    protected $primaryKey = 'MA_NGUOI_DUNG';
    protected $fillable = [
        'TEN',
        'CMND',
        'SDT',
        'NGAY_SINH',
        'DIA_CHI',
        'VAI_TRO',
        'id'
    ];
}
