<?php

namespace App\Http\Controllers;

use App\Http\Resources\OperationResource;
use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OperationController extends Controller
{
    public function index(Request $request)
    {
        // $products = Product::all();
        $sortField = $request->sortField ? $request->sortField : 'date';
        $sortOrder = $request->sortOrder ? $request->sortOrder : 'DESC';

        $req = Operation::orderBy($sortField, $sortOrder);
        $products = $req->paginate(20);

        return Inertia::render('Operations/Index', [
            'operations' => OperationResource::collection($products),
            'sortField'  => $sortField,
            'sortOrder'  => $sortOrder,
            'gerant'     => Auth::user()->can('gerant')
        ])->withViewData(['pageTitle' => 'Operations']);
    }
}
