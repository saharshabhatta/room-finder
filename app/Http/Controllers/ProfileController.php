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

        if (!$user) {
            return ApiResponse::error([
                'message' => 'User not authenticated.'
            ]);
        }

        $user->load('images');

        return ApiResponse::success([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'image_url' => $user->image ? asset('storage/' . $user->image->image_path) : null,
            ],
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
            if(!$request->hasFile('image') ||!$request->file('image')->isValid()) {
                return response()->json([
                    'message' => 'The image field is required.',
                ]);
            }

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $imageFile = $request->file('image');
                $path = $imageFile->store('users', 'public');

                $image = Image::where('user_id', $user->id)->first();

                if ($image && $image->image_path && Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }

                $data = [
                    'image_path' => $path,
                    'name' => $imageFile->getClientOriginalName(),
                ];

                if ($image) {
                    $image->update($data);
                } else {
                    $user->images()->create($data);
                }
            }

            if ($user->isDirty()) {
                $user->save();
            }

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
    public function destroy(Request $request): JsonResponse
    {
        try {
            $request->validateWithBag('userDeletion', [
                'password' => ['required', 'current_password'],
            ]);

            $user = $request->user();

            if (!$user) {
                return ApiResponse::error(['message' => 'Unauthenticated.'], 401);
            }

            $user->delete();

            return ApiResponse::success([
                'message' => 'User deleted successfully.',
            ]);

        } catch (Exception ) {
            return ApiResponse::error(['message' => 'Something went wrong.'], 500);
        }
    }
}
