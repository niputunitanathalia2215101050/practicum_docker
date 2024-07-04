<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request){
        try {
            $validasi = Validator::make($request->all(), [
                'name' => 'required',
                'nim' => 'required',
                'password' => 'required',
                'email' => 'required|email',
            ]);

            if ($validasi->fails()) {
                return response()->json([
                    'status' => 403,
                    'message' => $validasi->messages(),
                ], 403);
            }

            $user = User::where('name', $request->name)->where('email', $request->email)->where('nim', $request->nim)->first();
            if ($user && Hash::check($request->password, $user->password)){
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'status' => 200,
                    'message' => 'Berhasil login',
                    'token' => $token,
                    'data' => $user,
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'name, password, nim or username wrong'
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function register(Request $request){
        try {
            $validasi = Validator::make($request->all(), [
                'name' => 'required',
                'nim' => 'required',
                'password' => 'required',
                'email' => 'required|email',
            ]);

            if ($validasi->fails()) {
                return response()->json([
                    'status' => 403,
                    'message' => $validasi->messages(),
                ], 403);
            }
            $createUser = User::create([
                'name' => $request->name,
                'nim' => $request->nim,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'Berhasil register',
                'data' => $createUser,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Berhasil logout',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
