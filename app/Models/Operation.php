<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    use HasFactory;

    public const TYPE_ENTREE = "ENTREE";
    public const TYPE_SORTIE = "SORTIE";
    public const TYPE_TRANSFERT = "TRANSFERT";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'magazin_from_id',
        'magazin_to_id',
        'reference',
        'type',
        'date',
        'user_id',
        'description'
    ];

    public function getRouteKeyName()
    {
        return 'reference';
    }
    
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_operation')->withPivot('quantite', 'prix', 'piece');
    }
    
    public function magazinFrom()
    {
        return $this->belongsTo(Magazin::class, 'magazin_from_id');
    }
    
    public function magazinTo()
    {
        return $this->belongsTo(Magazin::class, 'magazin_to_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
