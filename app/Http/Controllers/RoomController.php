<?php

namespace App\Http\Controllers;

use App\Facades\ApiResponse;
use App\Http\Requests\RoomRequest;
use App\Models\Feature;
use App\Models\Image;
use App\Models\Room;
use App\Models\RoomType;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('images', 'features', 'roomType', 'user')->get();

        return ApiResponse::success([
            'data' => $rooms,
            'message' => 'Rooms retrieved successfully.'
        ]);
    }

    public function store(RoomRequest $request)
    {
        try {
            DB::beginTransaction();

            $room = Room::create(array_merge(
                $request->validated(),
                ['user_id' => auth()->id()]
            ));

            if ($request->has('features')) {
                foreach ($request->input('features') as $featureName) {
                    $feature = Feature::firstOrCreate([
                        'name' => $featureName,
                    ]);

                    $room->features()->attach($feature->id);
                }
            }

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('rooms', 'public');

                Image::create([
                    'room_id' => $room->id,
                    'image_path' => $path,
                    'name' => $request->file('image')->getClientOriginalName(),
                ]);
            }

            if ($request->has('room_type')) {
                $roomType = $request->input('room_type');

                RoomType::create([
                    'room_id' => $room->id,
                    'name' => $roomType['name'],
                    'description' => $roomType['description'],
                ]);
            }

            DB::commit();

            return ApiResponse::success([
                'data' => $room->load(['images', 'features', 'roomType']),
                'message' => 'Room created successfully.',
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::error([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function show(string $id)
    {
        $room = Room::with('images', 'features', 'roomType', 'user')->find($id);

        if (!$room) {
            return ApiResponse::error([
                'message' => 'Room not found.'
            ]);
        }

        return ApiResponse::success([
            'data' => $room,
            'message' => 'Room retrieved successfully.'
        ]);
    }

    public function update(RoomRequest $request, string $id)
    {
        try {
            DB::beginTransaction();

            $room = Room::find($id);

            if($room->user_id != auth()->id()){
                abort(403);
            }

            if (!$room) {
                return ApiResponse::error([
                    'message' => 'Room not found.'
                ]);
            }

            $room->update($request->validated());

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $path = $request->file('image')->store('rooms', 'public');

                $image = Image::where('room_id', $room->id)->first();

                if ($image && $image->image_path) {
                    if (Storage::disk('public')->exists($image->image_path)) {
                        Storage::disk('public')->delete($image->image_path);
                    }
                }

                if ($image) {
                    $image->update([
                        'image_path' => $path,
                        'name' => $request->file('image')->getClientOriginalName()
                    ]);
                } else {
                    Image::create([
                        'room_id' => $room->id,
                        'image_path' => $path,
                        'name' => $request->file('image')->getClientOriginalName()
                    ]);
                }
            }

            if ($request->has('room_type')) {
                $details = $request->input('room_type');

                if ($details) {
                    $roomType = RoomType::where('room_id', $room->id)->first();

                    if ($roomType) {
                        $roomType->update([
                            'name' => $details['name'],
                            'description' => $details['description'],
                        ]);
                    } else {
                        RoomType::create([
                            'room_id' => $room->id,
                            'name' => $details['name'],
                            'description' => $details['description'],
                        ]);
                    }
                }
            }

            if ($request->has('features')) {
                $featureIds = [];
                foreach ($request->input('features') as $featureName) {
                    $feature = Feature::firstOrCreate(['name' => $featureName]);
                    $featureIds[] = $feature->id;
                }

                $room->features()->sync($featureIds);
            }

            DB::commit();

            return ApiResponse::success([
                'data' => $room->load(['images', 'features', 'roomType']),
                'message' => 'Room updated successfully.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::error([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy(string $id)
    {
        $room = Room::find($id);

        if ($room->user_id != auth()->id()) {
            abort(403);
        }

        if (!$room) {
            return ApiResponse::error([
                'message' => 'Room not found.'
            ]);
        }
        $room->delete();

        return ApiResponse::success([
            'data' => $room,
            'message' => 'Room deleted successfully.'
        ]);
    }
}
