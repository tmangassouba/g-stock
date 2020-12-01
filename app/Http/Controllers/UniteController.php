<?php

namespace App\Http\Controllers;

use App\Models\Unite;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UniteController extends Controller
{
    public function index(Request $request)
    {
        $unites = Unite::orderBy('name')->get();

        return Inertia::render('Unites/Index', [
            'unites' => $unites
        ])->withViewData(['pageTitle' => 'Unités']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
                'name' => 'required|max:256|unique:unites,name',
            ],
            [
                'required' => 'Veillez renseigner un nom',
                'max'      => 'Trop de caractères',
                'unique'   => 'Ce nom existe déjà',
            ]
        );

        Unite::create($request->all());

        return redirect()->back()->with('message', 'Créée avec succès.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Devise  $devise
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Unite $unite)
    {
        $request->validate([
                'name' => 'required|max:256|unique:unites,name,'.$unite->id,
            ],
            [
                'required' => 'Veillez renseigner un nom',
                'max'      => 'Trop de caractères',
                'unique'   => 'Ce nom existe déjà',
            ]
        );

        $unite->update($request->all());

        return redirect()->back()->with('message', 'Modifiée avec succès.');
    }

    public function deleteUnites(Request $request)
    {
        foreach ($request->checkedRows as $_devise) {
            $unite = Unite::find($_devise['id']);
            if ($unite && $unite->id) {
                $unite->delete();
            }
        }

        return redirect()->back()->with('message', 'Unité(s) suprimée(s) avec succès.');
    }
}
