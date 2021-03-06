<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devise extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'symbole'
    ];

    public $timestamps = false;

    public function params()
    {
        return $this->hasOne('App\Models\Parametre');
    }
}
