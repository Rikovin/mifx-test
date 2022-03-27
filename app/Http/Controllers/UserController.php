<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try{
			DB::beginTransaction();
            //validate request data
            $data = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'password' => 'required',
            ]);
            
            //create user value by validated data
            $userCreate = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'api_token' => '',
                'is_admin' => $request->is_admin
            ]);

            //generating token
            $token = $userCreate->createToken('mifx')->plainTextToken;

            if ($userCreate) {
                DB::commit();
                return response()->json([
                    'message' => 'Success Creating User',
                    'user' => $userCreate,
                    'token' => $token,
                ], 201);
            } else {
                return response()->json(['message' => 'Error on Creating User'], 422);
            }
        } catch(\Exception $e){
            DB::rollback();
            return response()->json([
                'message' => 'UserController Catch Store',
                'code' => $e->getCode(),
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'trace' => $e->getTrace(),
            ], 422);
        }
    }

    public function logout(){
        //deleting token from user
        auth()->user()->tokens()->delete();
        return response()->json(['message' => 'User logged out'], 200);
    }

    public function login(Request $request){
        //validate request data
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        //check user email from email field, return first row
        $user = User::where('email', $fields['email'])->first();

        //check if user exits or password failed to compare 
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json(['message' => 'Invalid Credentials'], 400);
        }

        //generating token
        $token = $user->createToken('mifx')->plainTextToken;

        return response()->json(['message' => 'Login Success', 'user' => $user, 'token' => $token], 200);
    }
}
