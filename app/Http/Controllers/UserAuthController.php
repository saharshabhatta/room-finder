<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserAuthController extends Controller
{
//    function loginUser(Request $request){
//        return "Login Function";
//    }
//
//    function signupUser(Request $request){
//        return "Signup Function";
//    }

    function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
