<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = [
        'TEN_CUA_HANG',
        'SDT',
        'EMAIL',
        'DIA_CHI',
        'LOGO',
        'id'
    ];
}
