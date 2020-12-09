<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
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

    public function search(Request $request)
    {
        // $data['cible'] = $request->cible;
        $search = $request->search;
        $_data = null;
        if ($request->cible == 'Articles') {
            $_data = Product::with('unite')
                        ->where('designation', 'LIKE', '%'.$search.'%')
                        ->orWhere('code', 'LIKE', '%'.$search.'%')
                        ->orderBy('designation')
                        ->paginate(20);
        }
        if ($request->cible == 'Clients') {
            $_data = Customer::where('name', 'LIKE', '%'.$search.'%')
                        ->orWhere('code', 'LIKE', '%'.$search.'%')
                        ->orderBy('name')
                        ->paginate(20);
        }
        if ($request->cible == 'Operations') {
            $_data = Operation::where('reference', 'LIKE', '%'.$search.'%')
                        ->orderBy('reference')
                        ->paginate(20);
        }
        if ($request->cible == 'Factures') {
            $_data = Invoice::where('reference', 'LIKE', '%'.$search.'%')
                        ->orderBy('reference')
                        ->paginate(20);
        }

        // $data['data']  = $_data->getCollection();
        return $_data ? $_data->getCollection() : [];
    }
}
