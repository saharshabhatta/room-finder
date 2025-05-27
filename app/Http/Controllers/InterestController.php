<?php

namespace App\Http\Controllers;

use App\Facades\ApiResponse;
use App\Models\Interest;
use App\Models\Room;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InterestController extends Controller
{
    public function toggleInterest(Request $request)
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

            $interest=Interest::where('user_id', $user->id)
                ->where('room_id', $roomId)
                ->first();

            if ($interest) {
                $interest->delete();

                DB::commit();
                return ApiResponse::success('Interest removed.');
            }else{
                Interest::create([
                    'user_id'=>$user->id,
                    'room_id'=>$roomId,
                ]);
                DB::commit();
                return ApiResponse::success('Interest added.');
            }
        }catch (Exception $exception){
            DB::rollBack();
            return ApiResponse::error($exception->getMessage());
        }
    }

    public function getInterests()
    {
        $user = auth()->user();

        $interest=Interest::with('room')
            ->where('user_id', $user->id)
            ->get();

        return ApiResponse::success($interest);
    }

    public function getInterestById($id)
    {
        $user = auth()->user();

        $interest = Interest::with('room', 'user')->find($id);

        if (!$interest) {
            return ApiResponse::error('Interest not found.');
        }

        if ($interest->room->user_id !== $user->id) {
            return ApiResponse::error('Unauthorized access.', 403);
        }

        return ApiResponse::success($interest);
    }


    public function getInterestsByRooms()
    {
        $user = auth()->user();

        $interests = Interest::where('user_id', $user->id)
            ->orWhereHas('room', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['room', 'user'])
            ->get();

        if ($interests->isEmpty()) {
            return ApiResponse::error('Interest not found.', 400);
        }

        return ApiResponse::success([
            'data' => $interests,
            'message' => 'Interests retrieved successfully.'
        ]);
    }
}
