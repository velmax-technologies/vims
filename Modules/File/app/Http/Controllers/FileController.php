<?php

namespace Modules\File\Http\Controllers;

use Laravel\Pail\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\File\Http\Requests\FileRequest;
use Modules\File\Transformers\FileUploadResource;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

        return response()->json([]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FileRequest $request)
    {
        $request->validated(); // Validate the request using the FileRequest rules

        if (!Auth::user()->can('manage items')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Handle file upload
        $file = $request->file('file');
        $fileName = 'items.'.$file->getClientOriginalExtension(); 
        //$path = Storage::disk('public')->putFile('uploads', $file); // Store in 'storage/app/public/uploads'
        $filePath = $file->storeAs('uploads', $fileName, 'public'); // 'public' is the disk name

        // Optionally, save file path or other details to a database
        // e.g., File::create(['name' => $file->getClientOriginalName(), 'path' => $path]);

        return FileUploadResource::make(null);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        //

        return response()->json([]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //

        return response()->json([]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //

        return response()->json([]);
    }
}
