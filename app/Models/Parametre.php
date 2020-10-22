<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametre extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'adresse_1',
        'adresse_2',
        'ville',
        'pays',
        'phone',
        'faxe',
        'site',
        'email',
        'image',
        'devise_id'
    ];

    public $timestamps = false;

    public function devise()
    {
        return $this->belongsTo('App\Models\Devise');
    }
}
