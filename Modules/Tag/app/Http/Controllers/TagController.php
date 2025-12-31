<?php

namespace Modules\Tag\Http\Controllers;

use Spatie\Tags\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponseFormatTrait;
use Illuminate\Database\QueryException;
use Modules\Tag\Http\Requests\TagRequest;
use Modules\Tag\Transformers\TagResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TagController extends Controller
{
    use ApiResponseFormatTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
       $tags = Tag::ordered()->get();
       if($request->has('type')){
               $tags = Tag::getWithType('itemCategoryTag');

       }
       return TagResource::collection($tags)->additional($this->preparedResponse('index'));
    }

       /**
     * Store a newly created resource in storage.
     */
    public function store(TagRequest $request) {
        $request->validated();

        try{
            $tag = Tag::findOrCreate($request->name, $request->type);
            return TagResource::make($tag)->additional($this->preparedResponse('store'));
        
        } catch (QueryException $queryException) {
            return $this->queryExceptionResponse($queryException);
        }

        
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        try {
            $tag = Tag::findOrFail($id);
            return TagResource::make($tag)->additional($this->preparedResponse('show'));
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
        try {
            $tag = Tag::findOrFail($id);
            $tag->delete();
            return TagResource::make($tag)->additional($this->preparedResponse('destroy'));
        } catch (ModelNotFoundException $modelException) {
            return $this->recordNotFoundResponse($modelException);
        } catch (QueryException $queryException) {
            return $this->queryExceptionResponse($queryException);
        }
    }
}
