<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'code',
        'title',
        'name',
        'fisrt_name',
        'last_name',
        'address',
        'city',
        'telephone',
        'mobile',
        'email',
        'website',
        'company'
    ];

    public function getRouteKeyName()
    {
        return 'code';
    }
}
