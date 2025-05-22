<?php

namespace App\Http\Controllers;

use App\Facades\ApiResponse;
use App\Http\Requests\FeatureRequest;
use App\Models\Feature;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $features = Feature::where('user_id', 1)->get();

        return ApiResponse::success([
            'data' => $features,
            'message' => 'Feature list retrieved successfully for user ID 1'
        ]);
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

    public function store(FeatureRequest $request)
    {
        try {
            DB::beginTransaction();

            $feature = Feature::create($request->validated());

            DB::commit();

            return ApiResponse::success([
                'data' => $feature,
                'message' => 'Feature created successfully'
            ]);
        } catch (Exception $e) {
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
        $feature = Feature::find($id);

        if(!$feature){
            return ApiResponse::error([
                'message' => 'Feature not found'
            ]);
        }
        return ApiResponse::success([
            'data' => $feature,
            'message' => 'Feature retrieved successfully'
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
    public function update(FeatureRequest $request, string $id)
    {
        $feature=Feature::find($id);

        if(!$feature){
            return ApiResponse::error([
                'message' => 'Feature not found'
            ]);
        }
        $feature->update($request->validated());
        return ApiResponse::success([
            'data' => $feature,
            'message' => 'Feature updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $feature = Feature::find($id);
        if(!$feature){
            return ApiResponse::error([
                'message' => 'Feature not found'
            ]);
        }
        $feature->delete();

        return ApiResponse::success([
            'data' => $feature,
            'message' => 'Feature deleted successfully'
        ]);
    }
}
