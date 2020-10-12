<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation',
        'code',
        'ref_fabricant',
        'description',
        'stock_min',
        'stock_max',
        'unite_id',
        'prix',
        'quantite'
    ];

    public function getRouteKeyName()
    {
        return 'code';
    }

    public function unite()
    {
        return $this->belongsTo('App\Models\Unite');
    }
}
