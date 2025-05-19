<?php

namespace App\Http\Controllers;

use App\Facades\ApiResponse;
use App\Models\Favourite;
use App\Models\Room;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavouriteController extends Controller
{
    public function toggleFavourite(Request $request)
    {
        $user = auth()->user();

        try {
            DB::beginTransaction();

            $roomId = $request->input('room_id');
            if (!$roomId) {
                return ApiResponse::error('Room ID is required.');
            }

            $room = Room::find($roomId);
            if (!$room) {
                return ApiResponse::error('Room not found.');
            }

            $favourite = Favourite::where('user_id', $user->id)
                ->where('room_id', $roomId)
                ->first();

            if ($favourite) {
                $favourite->delete();

                DB::commit();
                return ApiResponse::success([
                    'message' => 'Removed from favourites.'
                ]);
            } else {
                Favourite::create([
                    'user_id' => $user->id,
                    'room_id' => $roomId
                ]);

                DB::commit();
                return ApiResponse::success([
                    'message' => 'Added to favourites.'
                ]);
            }
        } catch (Exception $exception) {
            DB::rollBack();
            return ApiResponse::error([
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function index()
    {
        $user = auth()->user();

        $favourites = Favourite::with('room')
        ->where('user_id', $user->id)
            ->get();

        return ApiResponse::success([
            'favourites' => $favourites
        ]);
    }
}
