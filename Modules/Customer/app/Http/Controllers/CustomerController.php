<?php

namespace Modules\Customer\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponseFormatTrait;
use Illuminate\Database\QueryException;
use Modules\Customer\Http\Requests\CustomerRequest;
use Modules\Customer\Transformers\CustomerResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CustomerController extends Controller
{
    use ApiResponseFormatTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::all();
        return (CustomerResource::collection($customers))->additional($this->preparedResponse('index'));
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerRequest $request)
    {
        $request->validated();

        if (!auth()->user()->can('manage customers')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $customer = Customer::create($request->all());

        // Log the activity of customer creation
        activity('customer created')->causedBy(auth()->user())->log('User ' . auth()->user()->username . ' created a new customer: ' . $customer->name);

        return (new CustomerResource($customer))->additional($this->preparedResponse('store'));
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        return (new CustomerResource($customer))->additional($this->preparedResponse('show'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $request, $id)
    {
         $request->validated();

        if (!auth()->user()->can('manage customers')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $customer = Customer::findOrFail($id);
            $customer->update($request->all());

            // Log the activity of customer update
            activity('customer updated')->causedBy(auth()->user())->log('User ' . auth()->user()->username . ' updated customer: ' . $customer->name);

            return (new CustomerResource($customer))->additional($this->preparedResponse('update'));
        } catch (ModelNotFoundException $modelException) {
            return $this->recordNotFoundResponse($modelException);
        } catch (QueryException $queryException) {
            return $this->queryExceptionResponse($queryException);
        }   
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('manage customers')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();

            // Log the activity of customer deletion
            activity('customer deleted')->causedBy(auth()->user())->log('User ' . auth()->user()->username . ' deleted customer: ' . $customer->name);

            return response()->json($this->preparedResponse('destroy'));
        } catch (ModelNotFoundException $modelException) {
            return $this->recordNotFoundResponse($modelException);
        } catch (QueryException $queryException) {
            return $this->queryExceptionResponse($queryException);
        }
    }
}
