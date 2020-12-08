<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sortField = $request->sortField ? $request->sortField : 'date';
        $sortOrder = $request->sortOrder ? $request->sortOrder : 'desc';

        $req = Invoice::with('customer', 'user')->orderBy($sortField, $sortOrder);
        $invoices = $req->paginate(50);

        return Inertia::render('Factures/Index', [
            'invoices' => InvoiceResource::collection($invoices),
            'sortField' => $sortField,
            'sortOrder' => $sortOrder,
            'can_edit'  => true
        ])->withViewData(['pageTitle' => 'Factures']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Factures/Form', [
            'invoice' => new Invoice(),
            'gerant'     => Auth::user()->can('gerant')
        ])->withViewData(['pageTitle' => 'Ajouter facture']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InvoiceRequest $request)
    {
        $user = $request->user();
        $invoice = Invoice::create([
            'statut'       => $request->statut,
            'customer_id'  => $request->customer_id,
            'date'         => substr($request->date, 0, 10),
            'description'  => $request->description,
            'user_id'      => $user ? $user->id : null,
        ]);

        foreach ($request->products as $_produit) {
            if ($_produit['produit_id']) {
                $invoice->products()->attach(
                    $_produit['produit_id'],
                    [
                        'prix'     => $_produit['prix'],
                        'quantite' => $_produit['quantite'],
                    ]
                );
            }
        }

        return redirect()->route('factures.edit', ['invoice' => $invoice]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        return Inertia::render('Factures/Show', [
            'invoice' => new InvoiceResource($invoice),
            'canEdit' => true
        ])->withViewData(['pageTitle' => 'Facture']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        return Inertia::render('Factures/Form', [
            'invoice' => new InvoiceResource($invoice),
            'gerant'     => Auth::user()->can('gerant')
        ])->withViewData(['pageTitle' => 'Ajouter facture']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(InvoiceRequest $request, Invoice $invoice)
    {
        $invoice->update([
            'statut'       => $request->statut,
            'customer_id'  => $request->customer_id,
            'date'         => substr($request->date, 0, 10),
            'description'  => $request->description
        ]);

        foreach ($request->products as $_product) {
            if ($_product['produit_id']) {
                $invoice->products()->syncWithoutDetaching([$_product['produit_id'] =>
                    [
                        'prix'     => $_product['prix'],
                        'quantite' => $_product['quantite']
                    ]
                ]);
            }
        }

        return redirect()->back()->with('message', 'Modifiée avec succès.');
        // return redirect()->route('factures.edit', ['invoice' => $invoice]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('factures.index');
    }
}
