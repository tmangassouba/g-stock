<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $operations = Operation::all();
        $stock = 0;
        foreach ($operations as $operation) {
            $products = $operation->products;
            foreach ($products as $product) {
                if ($operation->type == Operation::TYPE_ENTREE && $product->pivot) {
                    $stock += $product->pivot->quantite;
                }
                if ($operation->type == Operation::TYPE_SORTIE && $product->pivot) {
                    $stock -= $product->pivot->quantite;
                }
            }
        }

        $couts = 0;
        foreach ($operations as $operation) {
            $products = $operation->products;
            foreach ($products as $product) {
                if ($operation->type == Operation::TYPE_ENTREE && $product->pivot) {
                    $couts += $product->pivot->quantite * $product->pivot->prix;
                }
            }
        }

        return Inertia::render('Dashboard', [
            'operations' => Operation::count(),
            'products'   => Product::count(),
            'stock'      => $stock,
            'couts'      => $couts,
        ]);
    }
}
