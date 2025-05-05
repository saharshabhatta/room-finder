<?php

namespace App\Http\Controllers;

use App\Facades\ApiResponse;
use App\Http\Requests\RoomTypeRequest;
use App\Models\RoomType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ApiResponse::success([
            'data' => RoomType::all(),
            'message' => 'Room Type List retrieved successfully.'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoomTypeRequest $request)
    {
        try{
            DB::beginTransaction();

            $roomType=RoomType::create($request->validated());

            DB::commit();

            return ApiResponse::success([
                'data' => $roomType,
                'message' => 'Room Type created successfully.'
            ]);
        }catch(Exception $e){
            DB::rollBack();

            return ApiResponse::error([
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $roomType=RoomType::find($id);

        if(!$roomType){
            return ApiResponse::error([
                'message' => 'Room type not found.'
            ]);
        }
        return ApiResponse::success([
            'data' => $roomType,
            'message' => 'Room type retrieved successfully.'
        ]);
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
    public function update(RoomTypeRequest $request, string $id)
    {
        $roomType = RoomType::find($id);

       if(!$roomType){
           return ApiResponse::error([
               'message' => 'Room type not found.'
           ]);
       }
       $roomType->update($request->validated());
       return ApiResponse::success([
           'data' => $roomType,
           'message' => 'Room type updated successfully.'
       ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $roomType=RoomType::find($id);
        if(!$roomType){
            return ApiResponse::error([
                'message' => 'Room type not found.'
            ]);
        }
        $roomType->delete();
        return ApiResponse::success([
            'data'=>$roomType,
            'message' => 'Room type deleted successfully.'
        ]);
    }
}
