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
use App\Models\Favourite;
use Illuminate\Http\Request;

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
                $featureIds = [];

                foreach ($request->features as $feature) {
                    if (is_numeric($feature)) {
                        $featureModel = Feature::find((int) $feature);
                    } else {
                        $featureModel = Feature::firstOrCreate(['name' => $feature]);
                    }

                    if ($featureModel) {
                        $featureIds[] = $featureModel->id;
                    }
                }

                $room->features()->attach($featureIds);
            }

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('rooms', 'public');

                Image::create([
                    'room_id' => $room->id,
                    'image_path' => $path,
                    'name' => $request->file('image')->getClientOriginalName(),
                ]);
            }

            if ($request->has('room_type_id')) {
                $roomType = RoomType::find($request->room_type_id);
                if ($roomType) {
                    RoomType::create([
                        'room_id' => $room->id,
                        'name' => $roomType->name,
                        'description' => $roomType->description,
                    ]);
                }
            } elseif ($request->has('room_type')) {
                $roomTypeData = $request->room_type;

                RoomType::create([
                    'room_id' => $room->id,
                    'name' => $roomTypeData['name'] ?? 'Unnamed',
                    'description' => $roomTypeData['description'] ?? '',
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

            if (!$room) {
                return ApiResponse::error([
                    'message' => 'Room not found.'
                ]);
            }

            if ($room->user_id !== auth()->id()) {
                return ApiResponse::error([
                    'message' => 'Unauthorized.'
                ], 403);
            }

            $room->update($request->validated());

            if ($request->has('features')) {
                $featureIds = [];

                foreach ($request->features as $feature) {
                    if (is_numeric($feature)) {
                        $featureModel = Feature::find((int) $feature);
                    } else {
                        $featureModel = Feature::firstOrCreate(['name' => $feature]);
                    }

                    if ($featureModel) {
                        $featureIds[] = $featureModel->id;
                    }
                }

                $room->features()->sync($featureIds);
            }

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $path = $request->file('image')->store('rooms', 'public');

                $image = Image::where('room_id', $room->id)->first();

                if ($image && Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
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

            if ($request->has('room_type_id')) {
                $roomType = RoomType::find($request->room_type_id);
                if ($roomType) {
                    $existing = RoomType::where('room_id', $room->id)->first();

                    if ($existing) {
                        $existing->update([
                            'name' => $roomType->name,
                            'description' => $roomType->description,
                        ]);
                    } else {
                        RoomType::create([
                            'room_id' => $room->id,
                            'name' => $roomType->name,
                            'description' => $roomType->description,
                        ]);
                    }
                }
            } elseif ($request->has('room_type')) {
                $roomTypeData = $request->room_type;

                $existing = RoomType::where('room_id', $room->id)->first();

                if ($existing) {
                    $existing->update([
                        'name' => $roomTypeData['name'] ?? 'Unnamed',
                        'description' => $roomTypeData['description'] ?? '',
                    ]);
                } else {
                    RoomType::create([
                        'room_id' => $room->id,
                        'name' => $roomTypeData['name'] ?? 'Unnamed',
                        'description' => $roomTypeData['description'] ?? '',
                    ]);
                }
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
                return ApiResponse::error([
                    'message' => 'Unauthorized.'
                ]);
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

    public function search()
    {
        $query = request('q');

        $rooms = Room::with(['roomType', 'features'])
            ->where(function ($q) use ($query) {
                $q->where('description', 'like', '%' . $query . '%')
                    ->orWhere('location', 'like', '%' . $query . '%')
                    ->orWhere('district', 'like', '%' . $query . '%')
                    ->orWhere('province', 'like', '%' . $query . '%')
                    ->orWhereHas('roomType', function($q) use ($query) {
                        $q->where('name', 'like', '%' . $query . '%')
                            ->orWhere('description', 'like', '%' . $query . '%');
                    })
                    ->orWhereHas('features', function($q) use ($query) {
                        $q->where('name', 'like', '%' . $query . '%');
                    });
            })
            ->get();

        if ($rooms->isNotEmpty()) {
            return ApiResponse::success([
                'data' => $rooms,
                'message' => 'Rooms retrieved successfully.'
            ]);
        } else {
            return ApiResponse::error([
                'message' => 'No matching rooms found.'
            ]);
        }
    }

    public function filter(Request $request)
    {
        $minRent = $request->input('min_rent');
        $maxRent = $request->input('max_rent');

        $query = Room::query();

        if ($minRent !== null) {
            $query->where('rent', '>=', $minRent);
        }

        if ($maxRent !== null) {
            $query->where('rent', '<=', $maxRent);
        }

        $rooms = $query->get();

        if ($rooms->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No matching room found.'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $rooms
        ]);
    }

    public function getUserRooms($userId)
    {
        $rooms = Room::with(['images', 'features', 'roomType'])
            ->where('user_id', $userId)
            ->get();
        if ($rooms->isNotEmpty()) {
            return ApiResponse::success([
                'data' => $rooms,
                'message' => 'Rooms retrieved successfully.'
            ]);
        }else{
            return ApiResponse::error([
                'message' => 'No rooms found.'
            ]);
        }
    }
}
