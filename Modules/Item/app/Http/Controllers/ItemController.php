<?php

namespace Modules\Item\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponseFormatTrait;
use Illuminate\Database\QueryException;
use Modules\Item\Http\Requests\ItemRequest;
use Modules\Item\Transformers\ItemResource;
use Modules\Item\Services\CreateItemService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ItemController extends Controller
{
    use ApiResponseFormatTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Item::all();

        return (ItemResource::collection($items))->additional($this->preparedResponse('index'));
    }

    

    /**
     * Store a newly created resource in storage.
     */
    public function store(ItemRequest $request) {
        $request->validated();

        $itemData = $request->toArray();

        (new CreateItemService())->create($itemData);
        
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        try {
            $item = Item::findOrFail($id);
            return (new ItemResource($item))->additional($this->preparedResponse('show'));
        } catch (ModelNotFoundException $modelException) {
            return $this->recordNotFoundResponse($modelException);
        } catch (QueryException $queryException) {
            return $this->queryExceptionResponse($queryException);
        }
    }

    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {
        if (!auth()->user()->can('manage items')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $item = Item::findOrFail($id);
            // if($item->sale_items()->exists()){
            //     return response()->json([
            //         'message' => 'Cannot delete item associated with sales',
            //         'status' => 'failed'
            //     ], 400);
            // }
            $item->delete();

            // Log the activity of item deletion
            activity('item deleted')->causedBy(auth()->user())->log('User ' . auth()->user()->username . ' deleted item: ' . $item->name);

            return response()->json([
                'message' => 'Item deleted successfully',
                'status' => 'success'
            ], 200);
        } catch (ModelNotFoundException $modelException) {
            return $this->recordNotFoundResponse($modelException);
        } catch (QueryException $queryException) {
            return $this->queryExceptionResponse($queryException);
        }
    }
}
