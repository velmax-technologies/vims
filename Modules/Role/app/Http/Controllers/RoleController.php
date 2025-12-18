<?php

namespace Modules\Role\Http\Controllers;

use App\Enums\ApiStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponseFormatTrait;
use Illuminate\Database\QueryException;
use Modules\Role\Http\Requests\RoleRequest;
use Modules\Role\Transformers\RoleResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoleController extends Controller
{

        use ApiResponseFormatTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        return (RoleResource::collection($roles))->additional($this->preparedResponse('index'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
         $request->validated();

        DB::beginTransaction();

        try {
            $role = Role::create($request->all());
            // assign permissions if set
            if ($request->has('permissions')) {
                $role->syncPermissions($request->input('permissions'));
            }

            // log the creation of the role
            activity()
                ->performedOn($role)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $role->getAttributes()])
                ->log('role created');

            DB::commit(); 

            return (new RoleResource($role))->additional($this->preparedResponse('store'));
        } catch (\Exception $e) {
            DB::rollBack(); 
            return $this->errorResponse(
                ApiStatus::ERROR,
                422,
                'Failed to create role',
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        try {
            $role = Role::findOrFail($id);
            return (RoleResource::make($role))->additional($this->preparedResponse('show'));
        } catch (ModelNotFoundException $modelException) {
            return $this->recordNotFoundResponse($modelException);
        } catch (QueryException $queryException) {
            return $this->queryExceptionResponse($queryException);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, $id)
    {
        $request->validated();

        try {
            $role = Role::findOrFail($id);
            $role->update($request->all());

            // assign or update permissions if set
            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            activity('role updated')->causedBy(Auth::user())->log('user ' . Auth::user()->username . ' updated role: ' . $role->name);

            return (new RoleResource($role))->additional($this->preparedResponse('update'));
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
        try {
            $role = Role::findOrFail($id);
            $role->delete();

            activity('role deleted')->causedBy(Auth::user())->log('user ' . Auth::user()->username . ' deleted role: ' . $role->name);

            return (new RoleResource($role))->additional($this->preparedResponse('destroy'));
        } catch (ModelNotFoundException $modelException) {
            return $this->recordNotFoundResponse($modelException);
        } catch (QueryException $queryException) {
            return $this->queryExceptionResponse($queryException);
        }
    }
}
