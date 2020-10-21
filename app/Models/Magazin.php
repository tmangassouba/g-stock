<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Magazin extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'adresse',
        'ville',
        'phone',
        'email',
        'actif'
    ];
}
