<?php

namespace App\Http\Controllers;

use App\Http\Resources\FileResource;
use App\Http\Resources\OperationResource;
use App\Models\File;
use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class OperationController extends Controller
{
    public function index(Request $request)
    {
        // $products = Product::all();
        $sortField = $request->sortField ? $request->sortField : 'date';
        $sortOrder = $request->sortOrder ? $request->sortOrder : 'DESC';

        $req = Operation::with('products', 'magazinFrom', 'magazinTo', 'user')->orderBy($sortField, $sortOrder);
        $operations = $req->paginate(20);

        return Inertia::render('Operations/Index', [
            'operations' => OperationResource::collection($operations),
            'sortField'  => $sortField,
            'sortOrder'  => $sortOrder,
            'gerant'     => Auth::user()->can('gerant')
        ])->withViewData(['pageTitle' => 'Operations']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Operations/AddOperation', [
            'operation' => new Operation(),
            'gerant'     => Auth::user()->can('gerant')
        ])->withViewData(['pageTitle' => 'Ajouter opération']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $operation = Operation::create([
            'magazin_from_id' => $request->magazin_from_id,
            'magazin_to_id'   => $request->magazin_to_id,
            'description'     => $request->description,
            'type'            => $request->type,
            'date'            => substr($request->date, 0, 10),
            'user_id'         => $user ? $user->id : null,
        ]);

        foreach ($request->products as $_produit) {
            if ($_produit['produit_id']) {
                $operation->products()->attach(
                    $_produit['produit_id'],
                    [
                        'prix'     => $_produit['prix'],
                        'quantite' => $_produit['quantite'],
                        'piece'    => $_produit['piece'],
                    ]
                );
            }
        }

        return redirect()->route('operations.show', ['operation' => $operation]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Operation $operation)
    {
        return Inertia::render('Operations/showOperation', [
            'operation' => new OperationResource($operation),
            'gerant'     => Auth::user()->can('gerant')
        ])->withViewData(['pageTitle' => 'Opération']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Operation $operation)
    {
        return Inertia::render('Operations/AddOperation', [
            'operation' => new OperationResource($operation),
            'gerant'     => Auth::user()->can('gerant')
        ])->withViewData(['pageTitle' => 'Modifier opération']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Operation $operation)
    {
        $operation->update([
            'magazin_from_id' => $request->magazin_from_id,
            'magazin_to_id'   => $request->magazin_to_id,
            'description'     => $request->description,
            // 'type'            => $request->type,
            'date'            => substr($request->date, 0, 10),
        ]);

        foreach ($request->products as $_product) {
            if ($_product['produit_id']) {
                $operation->products()->syncWithoutDetaching([$_product['produit_id'] =>
                    [
                        'prix'     => $_product['prix'],
                        'quantite' => $_product['quantite'],
                        'piece'    => $_product['piece']
                    ]
                ]);
            }
        }

        return redirect()->route('operations.edit', ['operation' => $operation]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        foreach ($request->checkedRows as $product) {
            // $products[] = $product['id'];
            $operation = Operation::find($product['id']);
            if ($operation) {
                $operation->delete();
            }
        }
        // Product::destroy($products);
        return redirect()->route('operations.index')->with('message', 'Suprimées avec succès.');
    }

    public function docs(Operation $operation)
    {
        $docs = FileResource::collection($operation->files);
        return $docs;
    }

    public function uploadFiles(Request $request, Operation $operation)
    {
        $request->validate([
            'file' => 'required|max:2048',
            ],
            [ "required" => 'Veuillez selectionnez un fichier']
        );
        $file = $request->file;
        $extension = $file->extension();
        $name = $file->getClientOriginalName();
        $fileName = $operation->reference.'-'.time().'.'.$file->extension();
        Storage::putFileAs('public/files', $file, $fileName);

        $file = new File([
            'file_name' => $fileName,
            'name' => $name,
            'extension' => $extension
        ]);
        $operation->files()->save($file);

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

    public function validateOperation(Operation $operation)
    {
        $operation->validated = true;
        $operation->save();
        // return Redirect::route('operations.show', $operation)->with('message', 'Validée avec succès.');
        return redirect()->back()->with('message', 'Validée avec succès.');
    }
}
