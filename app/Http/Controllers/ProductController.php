<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Http\Resources\FileResource;
use App\Http\Resources\OperationResource;
use App\Http\Resources\ProductResource;
use App\Models\File;
use App\Models\Magazin;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // $products = Product::all();
        $sortField = $request->sortField ? $request->sortField : 'designation';
        $sortOrder = $request->sortOrder ? $request->sortOrder : 'asc';

        $req = Product::with('unite')->orderBy($sortField, $sortOrder);
        $products = $req->paginate(20);

        return Inertia::render('Articles/Index', [
            'products'  => ProductResource::collection($products),
            'sortField' => $sortField,
            'sortOrder' => $sortOrder,
            'admin'     => Auth::user()->can('admin')
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
            'admin'   => Auth::user()->can('admin')
        ])->withViewData(['pageTitle' => $code]);
    }

    public function update(ArticleRequest $request, Product $product)
    {
        $product->update([
            'designation'   => $request['designation'],
            'ref_fabricant' => $request['ref_fabricant'],
            'description'   => $request['description'],
            'stock_min'     => $request['stock_min'],
            'stock_max'     => $request['stock_max'],
            'stock_ouverture'     => $request['stock_ouverture'],
            'unite_id'      => $request['unite_id'],
            'prix'          => $request['prix'],
            'quantite'      => $request['quantite'],
        ]);

        return redirect()->back()->with('message', 'Produit modifié avec succès.');
    }

    public function destroy(Request $request, Product $product)
    {
        if ($product->delete()) {
            if ($product->image) {
                Storage::delete('public/products/'.$product->image);
            }
            return redirect('/articles');
        }
        abort(500);
    }

    public function deleteProducts(Request $request)
    {
        // $products = [];
        foreach ($request->checkedRows as $product) {
            // $products[] = $product['id'];
            $product = Product::find($product['id']);
            if ($product && $product->delete()) {
                if ($product->image) {
                    Storage::delete('public/products/'.$product->image);
                }
            }
        }
        // Product::destroy($products);
        return redirect()->back()->with('message', 'Produits suprimés avec succès.');
    }

    public function changeImage(Request $request, Product $product)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imageName = $product->code.'-'.time().'.'.$request->photo->extension();
        // $request->photo->move(public_path('products'), $imageName);
        Storage::putFileAs('public/products', $request->file('photo'), $imageName);

        $oldeName = $product->image;
        $product->image = $imageName;
        $product->save();
        if ($oldeName) {
            Storage::delete('public/products/'.$oldeName);
        }

        return redirect()->back()->with('message', 'Modifié avec succès.');
    }

    public function deleteImage(Product $product)
    {
        if ($product->image) {
            Storage::delete('public/products/'.$product->image);
        }
        $product->image = '';
        $product->save();

        return redirect()->back()->with('message', 'Modifié avec succès.');
    }

    public function stocks(Product $product)
    {
        $stocks = [];
        $magazins = Magazin::orderBy('name')->get();
        foreach ( $magazins as $magazin) {
            $stock['name']  = $magazin->name;
            $stock['stock'] = $product->stockByMagazin($magazin->id);

            $stocks[] = $stock;
        }
        return $stocks;
    }

    public function docs(Product $product)
    {
        $docs = FileResource::collection($product->files);
        return $docs;
    }

    public function uploadFiles(Request $request, Product $product)
    {
        $request->validate([
            'file' => 'required|max:2048',
            ],
            [ "required" => 'Veuillez selectionnez un fichier']
        );
        $file = $request->file;
        $extension = $file->extension();
        $name = $file->getClientOriginalName();
        $fileName = $product->code.'-'.time().'.'.$file->extension();
        Storage::putFileAs('public/files', $file, $fileName);

        $file = new File([
            'file_name' => $fileName,
            'name' => $name,
            'extension' => $extension
        ]);
        $product->files()->save($file);

        return redirect()->back()->with('message', 'Ajouté avec succès.');
    }

    public function deleteFiles(Request $request)
    {
        // $products = [];
        foreach ($request->checkedRows as $file) {
            // $products[] = $product['id'];
            $_file = File::find($file['id']);
            if ($_file && $_file->delete()) {
                Storage::delete('public/files/'.$_file->file_name);
            }
        }
        // Product::destroy($products);
        return redirect()->back()->with('message', 'Fichiers suprimés avec succès.');
    }

    public function operations(Request $request, Product $product)
    {
        $sortField = $request->sortField ? $request->sortField : 'date';
        $sortOrder = $request->sortOrder ? $request->sortOrder : 'DESC';

        $req = $product->operations()->with('products', 'magazinFrom', 'magazinTo', 'user')->orderBy($sortField, $sortOrder);
        $operations = $req->paginate(20);

        return Inertia::render('Articles/Operations', [
            'product'    => $product,
            'operations' => OperationResource::collection($operations),
            'sortField'  => $sortField,
            'sortOrder'  => $sortOrder,
        ])->withViewData(['pageTitle' => 'Operations']);
    }
}
