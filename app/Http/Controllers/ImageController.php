<?php

namespace App\Http\Controllers;

use App\Facades\ApiResponse;
use App\Http\Requests\ImageRequest;
use App\Models\Image;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ImageRequest $request)
    {
        try{
            DB::beginTransaction();

            $image = Image::create($request->validated());

            DB::commit();

            return ApiResponse::success([
                'data' => $image,
                'message' => 'Image added successfully.'
            ]);

        }catch (Exception $exception){
            DB::rollBack();
            return ApiResponse::error([
                'message' => $exception->getMessage()
            ]);
        }

    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ImageRequest $request, string $id)
    {
        $image=Image::find($id);
        if(!$image){
            return ApiResponse::error([
                'message' => 'Image not found.'
            ]);
        }
        $image->update($request->validated());
        return ApiResponse::success([
            'data' => $image,
            'message' => 'Image updated successfully.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
