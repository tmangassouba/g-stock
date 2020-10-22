<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParametreRequest;
use App\Http\Resources\ParametreResource;
use App\Models\Parametre;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ParametreController extends Controller
{
    public function index()
    {
        return Inertia::render('Parametre/Index')->withViewData(['pageTitle' => 'Profil organisation']);
    }
    
    public function entreprise()
    {
        $parametre = Parametre::first();
        if (!$parametre) {
            $parametre = Parametre::create([ 'name' => 'tiiStock' ]);
        }
        return new ParametreResource($parametre);
    }

    public function update(ParametreRequest $request)
    {
        $parametre = Parametre::first();
        if (!$parametre) {
            $parametre = Parametre::create([ 'name' => 'tiiStock' ]);
        }

        $parametre->update($request->all());
        return redirect()->back()->with('message', 'Modifié avec succès.');
    }
}
