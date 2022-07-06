<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $primaryKey = 'MA_REVIEW';
    protected $fillable = [
        'MA_SP',
        'MA_NGUOI_DUNG',
        'DANH_GIA'
    ];
}
