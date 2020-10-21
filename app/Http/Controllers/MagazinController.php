<?php

namespace App\Http\Controllers;

use App\Http\Requests\MagazinRequest;
use App\Http\Resources\MagazinResource;
use App\Models\Magazin;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MagazinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $magazins = Magazin::all();

        return Inertia::render('Magazins/Index', [
            'magazins'  => MagazinResource::collection($magazins),
        ])->withViewData(['pageTitle' => 'Magazins']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MagazinRequest $request)
    {
        Magazin::create($request->all());

        return redirect()->back()->with('message', 'Magazin créé avec succès.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Magazin  $magazin
     * @return \Illuminate\Http\Response
     */
    public function show(Magazin $magazin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Magazin  $magazin
     * @return \Illuminate\Http\Response
     */
    public function edit(Magazin $magazin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Magazin  $magazin
     * @return \Illuminate\Http\Response
     */
    public function update(MagazinRequest $request, Magazin $magazin)
    {
        $magazin->update($request->all());

        return redirect()->back()->with('message', 'Magazin modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Magazin  $magazin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Magazin $magazin)
    {
        //
    }

    public function deleteMagazins(Request $request)
    {
        foreach ($request->checkedRows as $product) {
            $product = Magazin::find($product['id']);
            if ($product && $product->id) {
                $product->delete();
            }
        }

        return redirect()->back()->with('message', 'Produits suprimés avec succès.');
    }
}
