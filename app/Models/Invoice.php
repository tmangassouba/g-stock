<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    /**
     * ROLES
     */
    public const STATUT_PAYEE = "PAYEÉE";
    public const STATUT_ACOMPTE = "ACOMPTE";
    public const STATUT_NON_PAYEE = "NON PAYEÉE";

    protected $fillable = [
        'reference',
        'statut',
        'customer_id',
        'date',
        'description'
    ];

    public function getRouteKeyName()
    {
        return 'reference';
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product')->withPivot('quantite', 'prix');
    }
}
