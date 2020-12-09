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

    public function invoices()
    {
        return $this->hasMany('App\Models\Invoice');
    }

    public function factureImpayee()
    {
        $total = 0;
        foreach ($this->invoices as $invoice) {
            if ($invoice->statut != Invoice::STATUT_PAYEE) {
                $total += $invoice->total() - $invoice->acompte;
            }
        }
        return $total;
    }
}
