<?php

namespace App\Http\Controllers\api\auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendResetPasswordJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Trait\ApiResponse;

class VerificationController extends Controller
{
    use ApiResponse;
    public function verifyEmail(Request $request, $id, $hash)
    {

        $user = User::findOrFail($id);

        if (!hash_equals(sha1($user->email), $hash)) {
            return response()->json(['message' => 'Invalid verification link.'], 403);
        }

        if ($user->email_verified_at != null) {
            return response()->json(['message' => 'Email is already verified.']);
        }

        $user->email_verified_at = now();
        $user->save();

        return response()->json(['message' => 'Email verified successfully.']);
    }

    public function sendResetLink(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->all(), 422);
            }

            $user = User::where('email', $request->email)->first();
            $otp = random_int(100000, 999999);
            $user->otp = Hash::make($otp);
            $user->otp_expires_at = now()->addMinutes(2);
            $user->save();

            SendResetPasswordJob::dispatch($user, $otp);
            return response()->json(['message' => 'Reset password link sent on your mail.',"for_testing" => $otp]);
        } catch (\Exception $e) {
            return $this->errorResponse([$e->getMessage()], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->otp, $user->otp) || now()->greaterThan($user->otp_expires_at)) {
            return $this->errorResponse('Otp expired', 403);
        }

        $user->password = Hash::make($request->password);
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        return $this->successResponse('Password reset successfully.',[]);
    }

}
