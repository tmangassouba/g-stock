<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sortField = $request->sortField ? $request->sortField : 'name';
        $sortOrder = $request->sortOrder ? $request->sortOrder : 'asc';

        $req = Customer::orderBy($sortField, $sortOrder);
        $customers = $req->paginate(50);

        return Inertia::render('Customers/Index', [
            'customers' => CustomerResource::collection($customers),
            'sortField' => $sortField,
            'sortOrder' => $sortOrder,
            'admin'     => Auth::user()->can('admin')
        ])->withViewData(['pageTitle' => 'Clients']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Customers/Form', [
            'customer' => new Customer(),
            'gerant'     => Auth::user()->can('gerant')
        ])->withViewData(['pageTitle' => 'Ajouter client']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {
        $customer = Customer::create($request->all());
        // return redirect()->route('clients.show', ['client' => $customer]);
        // return Redirect::route('clients.show', ['client' => $customer]);
        return Inertia::location(route('clients.show', ['customer' => $customer]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        return Inertia::render('Customers/Show', [
            'customer' => new CustomerResource($customer),
            'gerant'     => Auth::user()->can('gerant')
        ])->withViewData(['pageTitle' => 'Ajouter client']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        return Inertia::render('Customers/Form', [
            'customer' => $customer,
            'gerant'     => Auth::user()->can('gerant')
        ])->withViewData(['pageTitle' => 'Modifier client']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerRequest $request, Customer $customer)
    {
        $customer->update($request->all());
        return redirect()->back()->with('message', 'Modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        //
    }

    public function deleteClients(Request $request)
    {
        foreach ($request->checkedRows as $customer) {
            $_customer = Customer::find($customer['id']);
            if ($_customer) {
                $_customer->delete();
            }
        }
        // Product::destroy($products);
        return redirect()->route('clients.index')->with('message', 'Suprimé(s) avec succès.');
    }
}
