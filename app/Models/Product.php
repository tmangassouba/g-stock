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
        'stock_ouverture',
        'unite_id',
        'prix',
        'image',
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
    
    public function operations()
    {
        return $this->belongsToMany(Operation::class, 'product_operation')->withPivot('quantite', 'prix', 'piece');
    }

    public function stock()
    {
        // $stock = $this->stock_ouverture;
        $stock = 0;
        foreach ($this->operations as $operation) {
            if ($operation->type == Operation::TYPE_ENTREE && $operation->pivot) {
                $stock += $operation->pivot->quantite;
            }
            if ($operation->type == Operation::TYPE_SORTIE && $operation->pivot) {
                $stock -= $operation->pivot->quantite;
            }
        }
        return $stock;
    }

    public function stockByMagazin($idMagazin)
    {
        $stock = 0;
        foreach ($this->operations as $operation) {
            if ($operation->type == Operation::TYPE_ENTREE && $operation->pivot && $operation->magazin_to_id == $idMagazin) {
                $stock += $operation->pivot->quantite;
            }
            if ($operation->type == Operation::TYPE_SORTIE && $operation->pivot && $operation->magazin_from_id == $idMagazin) {
                $stock -= $operation->pivot->quantite;
            }
        }
        return $stock;
    }
}
