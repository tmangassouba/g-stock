<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeviseRequest;
use App\Http\Resources\DeviseResource;
use App\Models\Devise;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DeviseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $devises = Devise::all();

        return Inertia::render('Devises/Index', [
            'devises'  => DeviseResource::collection($devises),
        ])->withViewData(['pageTitle' => 'Devises']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeviseRequest $request)
    {
        Devise::create($request->all());

        return redirect()->back()->with('message', 'Devise créée avec succès.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Devise  $devise
     * @return \Illuminate\Http\Response
     */
    public function show(Devise $devise)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Devise  $devise
     * @return \Illuminate\Http\Response
     */
    public function edit(Devise $devise)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Devise  $devise
     * @return \Illuminate\Http\Response
     */
    public function update(DeviseRequest $request, Devise $devise)
    {
        $devise->update($request->all());

        return redirect()->back()->with('message', 'Devise modifiée avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Devise  $devise
     * @return \Illuminate\Http\Response
     */
    public function destroy(Devise $devise)
    {
        //
    }

    public function deleteDevises(Request $request)
    {
        foreach ($request->checkedRows as $_devise) {
            $devise = Devise::find($_devise['id']);
            if ($devise && $devise->id) {
                $devise->delete();
            }
        }

        return redirect()->back()->with('message', 'Devise(s) suprimée(s) avec succès.');
    }
}
