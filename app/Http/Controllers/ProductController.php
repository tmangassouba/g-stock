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

        $req = Product::orderBy($sortField, $sortOrder);
        $products = $req->paginate(20);

        return Inertia::render('Articles/Index', [
            'products' => ProductResource::collection($products),
            'sortField' => $sortField,
            'sortOrder' => $sortOrder,
        ])->withViewData(['pageTitle' => 'Articles']);
    }

    public function store(ArticleRequest $request)
    {
        Product::create($request->all());

        return redirect()->back()->with('message', 'Produit créé avec succès.');
    }

    public function view(Product $product)
    {
        $code = $product ? $product->designation : '';
        return Inertia::render('Articles/ProductView', [
            'product' => new ProductResource($product),
        ])->withViewData(['pageTitle' => $code]);
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

    public function deleteProducts(Request $request)
    {
        // $products = [];
        foreach ($request->checkedRows as $product) {
            // $products[] = $product['id'];
            $product = Product::find($product['id']);
            if ($product && $product->delete()) {
                // if ($product->image) {
                //     \File::delete('imgs/products/'.$product->image);
                // }
            }
        }
        // Product::destroy($products);
        return redirect()->back()->with('message', 'Produits suprimés avec succès.');
    }
}
