<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // $products = Product::all();
        $sortField = $request->sortField ? $request->sortField : 'designation';
        $sortOrder = $request->sortOrder ? $request->sortOrder : 'asc';
        $recherche = null;

        $req = Product::orderBy($sortField, $sortOrder);
        // if ($request->recherche) {
        //     $recherche = $request->recherche;
        //     $req = $req->where('designation', 'like', '%'.$recherche.'%');
        // }
        $products = $req->paginate(20);

        return Inertia::render('Articles/Index', [
            'products' => $products,
            'sortField' => $sortField,
            'sortOrder' => $sortOrder,
        ]);
    }

    public function store(ArticleRequest $request)
    {
        Product::create($request->all());

        return redirect()->back()->with('message', 'Produit créé avec succès.');
    }

    public function view(Product $product)
    {
        return Inertia::render('Articles/ProductView', [
            'product' => new ProductResource($product),
        ]);
    }

    // public function update(ArticleRequest $request)
    // {
    //     if ($request->has('id')) {
    //         Contact::find($request->input('id'))->update($request->all());
    //         return redirect()->back();
    //     }
    // }

    // public function destroy(Request $request)
    // {
    //     if ($request->has('id')) {
    //         Contact::find($request->input('id'))->delete();
    //         return redirect()->back();
    //     }
    // }
}
