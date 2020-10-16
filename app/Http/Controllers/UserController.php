<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\CreateUserNotification;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $products = Product::all();
        $sortField = $request->sortField ? $request->sortField : 'name';
        $sortOrder = $request->sortOrder ? $request->sortOrder : 'asc';

        $req = User::orderBy($sortField, $sortOrder);
        $users = $req->paginate(20);

        return Inertia::render('Users/Index', [
            'users'       => UserResource::collection($users),
            'sortField'   => $sortField,
            'sortOrder'   => $sortOrder,
            'admin'     => Auth::user()->can('admin'),
            'authUserId'  => Auth::id()
        ])->withViewData(['pageTitle' => 'Utilisateurs']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $password = Str::random(10);

        $user = User::create([
            'first_name' => $request['first_name'],
            'last_name'  => $request['last_name'],
            'email'      => $request['email'],
            'actif'      => $request['actif'],
            'phone'      => $request['phone'],
            'password'   => Hash::make($password),
        ]);
        $user->roles()->attach($request['roles']);
        $user->notify(new CreateUserNotification($password));

        return redirect()->back()->with('message', 'Utilisateur créé avec succès.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email'  =>  'required|unique:users,email,'.$user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        return redirect()->route('users.index')->with('successMessage', 'User was successfully updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('successMessage', 'User was successfully deleted!');
    }

    public function deleteProducts(Request $request)
    {
        // $products = [];
        foreach ($request->checkedRows as $product) {
            // $products[] = $product['id'];
            $product = User::find($product['id']);
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