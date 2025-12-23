<?php

namespace Modules\Menu\Http\Controllers;

use App\Models\Item;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponseFormatTrait;
use Illuminate\Database\QueryException;
use Modules\Item\Transformers\ItemResource;
use Modules\Menu\Http\Requests\MenuRequest;
use Modules\Menu\Transformers\MenuResource;
use Modules\Menu\Services\MenuCreateService;
use Modules\Menu\Services\MenuDeleteService;
use Modules\Menu\Services\MenuUpdateService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MenuController extends Controller
{
    use ApiResponseFormatTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $menus = Menu::with('menu_items')->get();
        return MenuResource::collection($menus)->additional($this->preparedResponse('index'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MenuRequest $request)
    {
        // created using ItemCreateService
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        // try {
        //     $menu = Menu::with('menu_items')->findOrFail($id);
        //     return (new MenuResource($menu))
        //         ->additional($this->preparedResponse('show'));
        // } catch (ModelNotFoundException $modelException) {
        //     return $this->recordNotFoundResponse($modelException);
        // } catch (QueryException $queryException) {
        //     return $this->queryExceptionResponse($queryException);
        // }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MenuRequest $request, $id)
    {
        
        if (!auth()->user()->can('manage items')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validated();

        try {
            $menu = (new MenuUpdateService())->update($request, Item::findOrFail($id));
            
            return (new MenuResource($menu))
                ->additional($this->preparedResponse('update'));
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
        if (!auth()->user()->can('manage items')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $menu = (new MenuDeleteService())->delete(Item::findOrFail($id));
           
            activity('menu deleted')->causedBy(Auth::user())->log('Menu item ' . Auth::user()->username . ' deleted menu item: ' . $menu->name);
            
            return MenuResource::make($menu)->additional($this->preparedResponse('destroy'));

        } catch (ModelNotFoundException $modelException) {
            return $this->recordNotFoundResponse($modelException);
        } catch (QueryException $queryException) {
            return $this->queryExceptionResponse($queryException);
        }
    }
}
