<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;
    protected $primaryKey = 'MA_HOP_DONG';
    protected $fillable = [
        'LOAI',
        'NGAY_KY',
        'NGAY_HIEU_LUC',
        'NGAY_KET_THUC',
        'GIAY_CHUNG_NHAN_AN_TOAN',
        'GIAY_PHEP_KINH_DOANH',
        'MA_NGUOI_DUNG'
    ];
}
