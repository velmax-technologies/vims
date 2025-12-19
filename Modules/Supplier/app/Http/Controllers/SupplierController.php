<?php

namespace Modules\Supplier\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponseFormatTrait;
use Illuminate\Database\QueryException;
use Modules\Supplier\Http\Requests\SupplierRequest;
use Modules\Supplier\Transformers\SupplierResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SupplierController extends Controller
{
    use ApiResponseFormatTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $suppliers = Supplier::all();
       return (SupplierResource::collection($suppliers))->additional($this->preparedResponse('index'));
    }

   
    /**
     * Store a newly created resource in storage.
     */
    public function store(SupplierRequest $request) {
        $request->validated();

        if (!auth()->user()->can('manage suppliers')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $supplier = Supplier::create($request->all());

        // Log the activity of supplier creation
        activity('supplier created')->causedBy(auth()->user())->log('User ' . auth()->user()->username . ' created a new supplier: ' . $supplier->name);

        return (new SupplierResource($supplier))->additional($this->preparedResponse('store'));
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            return (new SupplierResource($supplier))->additional($this->preparedResponse('show'));
        } catch (ModelNotFoundException $modelException) {
            return $this->recordNotFoundResponse($modelException);
        } catch (QueryException $queryException) {
            return $this->queryExceptionResponse($queryException);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SupplierRequest $request, $id) {
        $request->validated();

        if (!auth()->user()->can('manage suppliers')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->update($request->all());

            // Log the activity of supplier update
            activity('supplier updated')->causedBy(auth()->user())->log('User ' . auth()->user()->username . ' updated supplier: ' . $supplier->name);

            return (new SupplierResource($supplier))->additional($this->preparedResponse('update'));
        } catch (ModelNotFoundException $modelException) {
            return $this->recordNotFoundResponse($modelException);
        } catch (QueryException $queryException) {
            return $this->queryExceptionResponse($queryException);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {
        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->delete();

            // Log the activity of supplier deletion
            activity('supplier deleted')->causedBy(auth()->user())->log('User ' . auth()->user()->username . ' deleted supplier: ' . $supplier->name);

            return response()->json($this->deletionResponse(), 200);
        } catch (ModelNotFoundException $modelException) {
            return $this->recordNotFoundResponse($modelException);
        } catch (QueryException $queryException) {
            return $this->queryExceptionResponse($queryException);
        }
    }
}
