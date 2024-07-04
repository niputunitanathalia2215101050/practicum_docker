<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $token = Str::random(60);

        Log::info("Password reset token: $token");

        return response()->json(['message' => 'Password reset link sent to your email'], 200);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $passwordReset = DB::table('users')->where('email', $user->email)->first();

        if (!$passwordReset) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return response()->json(['message' => 'berhasil reset password'], 200);
    }

    public function deleteUser(User $user)
    {
        try {
            if (User::count() === 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete the last remaining user'
                ], 422);
            }
            $user->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'User deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'status' => 'error',
                'message' => $th
            ], 500);
        }
    }

    public function editUpdate(Request $request, User $user)
    {
        try {
            $validasi = Validator::make($request->all(), [
                'name' => 'string|max:255',
                'email' => 'string|email|max:255',
                'old_password' => 'string|min:8|nullable',
                'new_password' => 'string|min:8|nullable',
            ]);

            if ($validasi->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => $validasi->messages()
                ], 422);
            }

            $updateData = [];

            if ($request->filled('name')) {
                $updateData['name'] = $request->name;
            }

            if ($request->filled('email')) {
                $updateData['email'] = $request->email;
            }

            if ($request->filled('old_password') && $request->filled('new_password')) {
                if (!Hash::check($request->old_password, $user->password)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Password lama salah'
                    ], 422);
                }

                $updateData['password'] = Hash::make($request->new_password);
            }

            $user->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'User updated successfully'
            ], 200);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'status' => 'error',
                'message' => $th
            ], 500);
        }
    }
}
