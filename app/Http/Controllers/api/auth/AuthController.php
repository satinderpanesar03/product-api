<?php

namespace App\Http\Controllers\api\auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Jobs\SendEmailVerification;
use App\Models\User;
use Illuminate\Http\Request;
use App\Trait\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ApiResponse;
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|exists:users,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        try {
            if (!Auth::attempt($request->only('email', 'password'))) {
                return $this->errorResponse('Invalid credentials', 401);
            }

            $user = User::select('id','name','email')->where('email', $request->email)->first();
            $token = $user->createToken('user-access-token')->plainTextToken;

            return $this->successResponse('Login successful', [
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Login failed. Please try again.', 500);
        }
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            SendEmailVerification::dispatch($user);

            $token = $user->createToken('user-access-token')->plainTextToken;

            return $this->successResponse('User successfully registered, Verify your email to verify your account.', [
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return $this->successResponse('Logged out successfully.', []);
    }

}
