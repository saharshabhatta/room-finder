<?php

namespace App\Http\Controllers;

use App\Facades\ApiResponse;
use App\Http\Requests\SignupUserRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;


class dummyAPI extends Controller
{
//    function getData(){
//        return[
//            "name"=>"Saharsha",
//            "email"=>"saharsha@gmail.com"
//        ];
//    }
//
//    function list($id = null) {
//        $user = User::find($id);
//
//        if (!$user) {
//            return ApiResponse::setResponse([])
//                ->setMessage('User not found')
//                ->setCode(404)
//                ->error();
//        }
//
//        return ApiResponse::setResponse($user)
//            ->setMessage('User fetched successfully')
//            ->setCode(200)
//            ->success();
//    }
//
//    function getUser(){
//        $users = User::all();
//        if(!$users){
//            return ApiResponse::setResponse([])
//                ->setMessage('User not found')
//                ->setCode(404)
//                ->error();
//        }
//        return ApiResponse::success([
//            'data' => $users,
//            'message' => 'User fetched successfully'
//
//        ]);
//        return ApiResponse::setResponse($users)
//            ->setMessage('User fetched successfully')
//            ->setCode(200)
//
//            ->success();
//        return ApiResponse::success($users, 'User list fetched successfully');
//    }

//    function createUser(SignupUserRequest $request){
//        $rules = [
//            'name' => ['required', 'string', 'max:255'],
//            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
//            'password' => ['required'],
//        ];
//        $validate=Validator::make($request->all(), $rules);
//        if($validate->fails()){
//            return $validate->errors();
//        }
//        else{
//            $user = new User();
//            $user->name=$request->name;
//            $user->email=$request->email;
//            $user->email_verified_at=$request->email_verified_at;
//            $user->password=$request->password;
//            $user->created_at=$request->created_at;
//            $user->updated_at=$request->updated_at;
//
//            if($user->save()){
//                return ["result"=>"User has been created"];
//            }
//            else{
//                return ["result"=>"User could not be created"];
//            }
//        }
//    }
//
//    function updateUser(SignupUserRequest $request){
////        return "update user";
//
//        $rules = [
//            'name' => ['required', 'string', 'max:255'],
//            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
//            'password' => ['required'],
//        ];
//        $validate=Validator::make($request->all(), $rules);
//        if($validate->fails()){
//            return $validate->errors();
//        }
//        else {
//            $user = User::find($request->id);
//            $user->name = $request->name;
//            $user->email = $request->email;
//            $user->email_verified_at = $request->email_verified_at;
//            $user->password = $request->password;
//            $user->created_at = $request->created_at;
//            $user->updated_at = $request->updated_at;
//
//            if ($user->save()) {
//                return ["result" => "User has been updated"];
//            } else {
//                return ["result" => "User could not be updated"];
//            }
//        }
//    }
//
//    function deleteUser($id){
//        $user=User::destroy($id);
//        if($user){
//            return ["result"=>"User has been deleted"];
//        }
//        else{
//            return ["result"=>"User could not be deleted"];
//        }
//    }

    function loginUser(Request $request){
        try{
            DB::beginTransaction();
            $user=User::where('email',$request->email)->first();
            if(!$user || !Hash::check($request->password,$user->password)){
                return ["result"=>"Email or password is not correct", 'Success'=>false];
            }
            $success['token']=$user->createToken('MyApp')->plainTextToken;
            $user['name']=$user->name;

            DB::commit();
            return ['success'=>true,'result'=>$success,'message'=>'user logged in successfully', 'user'=>$user->load('roles')];
        }catch (Exception $e){
            DB::rollBack();
            return ["result"=>"Something went wrong", 'Success'=>false];
        }
    }

    function signupUser(SignupUserRequest $request)
    {
        try {
            DB::beginTransaction();
            $input = $request->validated();
            $input['password'] = Hash::make($input['password']);

            $user = User::create($input);
            $user->roles()->attach($request->role);

            $success['token'] = $user->createToken('MyApp')->plainTextToken;

            DB::commit();

            return [
                'success' => true,
                'result' => $success,
                'message' => 'user registered successfully',
                'user' => $user->load('roles')
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

//    public function signupUser(SignupUserRequest $request)
//    {
//        try {
//            $input = $request->validated();
//            $input['password'] = Hash::make($input['password']);
//            $user = User::create($input);
//
//            return ApiResponse::success([
//                'data' => $user,
//                'message' => 'User signed up and logged in successfully.',
//            ]);
//
//        } catch (Exception $exception) {
//            return ApiResponse::setResponse([
//                'message' => $exception->getMessage()
//            ]);
//        }
//    }
//
//    function  loginUser(Request $request)
//    {
//        $credentials = $request->validate([
//            'email' => 'required|email',
//            'password' => 'required',
//        ]);
////        $credentials=$request->only('email','password');
//
//        if (Auth::attempt($credentials)) {
//            $success=session()->regenerate();
//
//            return [
//                'success' => $success,
//                'message' => 'User logged in successfully',
//                'user' => Auth::user(),
//            ];
//        }
//
//        return [
//            'success' => false,
//            'message' => 'Email or password is not correct',
//        ];
//    }

    function searchUser($name){
        $user = User::where('name', 'like', '%'.$name.'%')->get();
        if($user){
            return ["result"=>"User found", "data"=>$user];
        }
        else{
            return ["result"=>"User not found"];
        }
    }
}
