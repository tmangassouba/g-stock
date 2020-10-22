<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParametreRequest;
use App\Http\Resources\ParametreResource;
use App\Models\Parametre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

    public function changeImage(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $parametre = Parametre::first();
        if (!$parametre) {
            $parametre = Parametre::create([ 'name' => 'tiiStock' ]);
        }

        $imageName = 'logo-'.time().'.'.$request->photo->extension();
        // $request->photo->move(public_path('products'), $imageName);
        Storage::putFileAs('public/', $request->file('photo'), $imageName);

        $oldeName = $parametre->image;
        $parametre->image = $imageName;
        $parametre->save();
        if ($oldeName) {
            Storage::delete('public/'.$oldeName);
        }

        return redirect()->back()->with('message', 'Modifié avec succès.');
    }

    public function deleteImage()
    {
        $parametre = Parametre::first();
        if (!$parametre) {
            $parametre = Parametre::create([ 'name' => 'tiiStock' ]);
        }

        if ($parametre->image) {
            Storage::delete('public/'.$parametre->image);
        }
        $parametre->image = '';
        $parametre->save();

        return redirect()->back()->with('message', 'Modifié avec succès.');
    }
}
