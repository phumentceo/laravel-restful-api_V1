<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->passes()){
            $credentials = [
                'email' => $request->email,
                "password" => $request->password,
            ];
            if(Auth::attempt($credentials)){

                $user = Auth::user();
                $token = $user->createToken("API Token")->plainTextToken;

                return response([
                    'status' => true,
                    'message' => 'Login successful',
                    'token' => $token,
                ]);
            }else{
                return response([
                    'status' => false,
                    'message' => 'email or password incorrect',
                 ],500);
            }

        }else{
            return response([
                'status' => false,
                'message' => 'email or password incorrect',
                'error' =>  $validator->errors()
             ],500);
        }
    }

    public function register(Request $request){

        $validator  = Validator::make($request->all(), [
            'name'  => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:4',
            'confirm_password' => 'required|same:password'
        ]);

        if($validator->passes()){

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);

            $user->save();

            $token = $user->createToken("API Token")->plainTextToken;


            return response()->json([
                'status' => true,
                'user'   => $user,
                'token'  => $token
            ]);

        }else{
            return response()->json([
                'status' => false,
                'message' => 'Please config the arrors',
                'errors'  => $validator->errors(),
            ],500);
        }

    }

    public function logout(){
        
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
        ]);

        
    }
}
