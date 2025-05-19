<?php

namespace App\Http\Controllers;

use App\Facades\ApiResponse;
use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggleFavorite(Request $request)
    {
        $user = auth()->user();

        $roomId = $request->input('room_id');
        if (!$roomId) {
            return ApiResponse::error('Room ID is required.', 400);
        }

        $favorite = Favorite::where('user_id', $user->id)
            ->where('room_id', $roomId)
            ->first();

        if ($favorite) {
            $favorite->delete();

            return ApiResponse::success([
                'message' => 'Removed from favorites.'
            ]);
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'room_id' => $roomId
            ]);

            return ApiResponse::success([
                'message' => 'Added to favorites.'
            ]);
        }
    }
}
