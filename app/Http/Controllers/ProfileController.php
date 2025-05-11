<?php

namespace App\Http\Controllers;

use App\Facades\ApiResponse;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Image;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): JsonResponse
    {
        $user = Auth::user();
        if($request->expectsJson()){
            return response()->json(ApiResponse::success($user));
    }
        if (!$user) {
            return ApiResponse::error([
                'message' => 'User not authenticated.'
            ]);
        }

        return ApiResponse::success([
            'data' => $user,
            'message' => 'User retrieved successfully.'
        ]);
    }


    /**
     * Update the user's profile information.
     */

    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'message' => 'User not found.',
                ], 404);
            }

            $user->fill($request->validated());

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $path = $request->file('image')->store('users', 'public');

                $image = Image::where('user_id', $user->id)->first();

                if ($image && $image->image_path && Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                 }

                if ($image) {
                    $image->update([
                        'image_path' => $path,
                        'name' => $request->file('image')->getClientOriginalName()
                    ]);
                } else {
                    Image::create([
                        'user_id' => $user->id,
                        'image_path' => $path,
                        'name' => $request->file('image')->getClientOriginalName()
                    ]);
                }
            }

            $user->save();

           return ApiResponse::success([
               'message' => 'Profile updated successfully.',
           ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the profile.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
