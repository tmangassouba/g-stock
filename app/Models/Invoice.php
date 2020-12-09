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
    public const STATUT_PAYEE = "PAYÉE";
    public const STATUT_ACOMPTE = "ACOMPTE";
    public const STATUT_NON_PAYEE = "NON PAYÉE";

    protected $fillable = [
        'reference',
        'statut',
        'customer_id',
        'date',
        'description',
        'user_id',
        'acompte'
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
        return $this->belongsToMany('App\Models\Product', 'product_invoice')->withPivot('quantite', 'prix');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function total()
    {
        $total = 0;
        foreach ($this->products as $product) {
            if ($product->pivot) {
                $total += $product->pivot->quantite * $product->pivot->prix;
            }
        }

        return $total;
    }
}
