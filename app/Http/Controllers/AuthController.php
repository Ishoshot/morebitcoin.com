<?php

namespace App\Http\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use App\Jobs\Admin\NewUserJob;
use App\Jobs\User\UserWelcomeAndVerifyJob;
use App\Models\Role;
use App\Models\User;
use App\Notifications\AdminNewUser;
use App\Notifications\ResetOTP;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {
        try {
            $role = Role::where('key', 'user')->first();

            $validator = Validator::make($request->all(), [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:users',
                'phone' => 'required|max:255',
                'password' => 'required|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $user = User::create([
                'role_id' => $role->id,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            // $otp = Otp::generate($user->email, 6, 10080);
            $otp = (new Otp)->generate($user->email, 6, 10080);

            $user->update(['verification_code' => $otp->token]);

            $user->profile()->create([
                'user_id' => $user->id,
                'phone' => $request->phone,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
            ]);

            dispatch(new UserWelcomeAndVerifyJob($user));
            dispatch(new NewUserJob($user));

            return response()->json(["user" => $user], 201);
        } catch (Exception $e) {
            Log::error("Registration Error" . "===" .  $e->getMessage());
        }
    }

    /**
     * Login a user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {

        try {
            $credentials = $request->only('email', 'password');

            $validator = Validator::make($credentials, [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken(env("APP_NAME"))->plainTextToken;
                $user['role'] = $user->role;
                return response()->json(['user' => $user, 'token' => $token], 200);
            } else {
                return response()->json(['error' => 'User not Unauthorised'], 401);
            }
        } catch (Exception $e) {
            Log::error("Login Error" . " === " .  $e->getMessage());
        }
    }

    /**
     * Verify a user's OTP
     *
     * @param  Request  $request
     * @return Response
     */
    public function verifyOtp(Request $request)
    {
        try {
            $user = User::where('id', $request->id)->first();

            $otp = (new Otp)->validate($user->email, $request->otp);

            if ($otp->status) {
                $user->update(['is_verified' => true]);
                return response()->json(['case' => 'success', 'message' => 'User Verified Successfully'], 200);
            } else {
                return response()->json(['case' => 'error', 'message' => 'Oops! User not Verified. Check the Code and try again'], 401);
            }
        } catch (Exception $e) {
            Log::error("Verify OTP Error" . " === " .  $e->getMessage());
        }
    }

    /**
     * Logout a user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function logout(Request $request)
    {
        try {
            Auth::user()->tokens()->delete();
            return response()->json(['message' => 'Successfully logged out'], 200);
        } catch (Exception $e) {
            Log::error("Logout Error" . " === " .  $e->getMessage());
        }
    }

    /**
     * Send Otp
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     *
     */
    public function sendOtp(Request $request)
    {
        //ensure email exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User with this email not found'], 404);
        }

        //generate otp
        $otp = (new Otp)->generate($user->email, 6, 10080);

        //send mail to the user
        try {
            $user->notify(new ResetOTP($otp->token));
            return response()->json(['message' => 'Otp sent successfully'], 200);
        } catch (\Throwable $th) {
            //log
            Log::error("Send Otp Error" . " === " .  $th->getMessage());
            return response()->json(['message' => 'Otp could not be sent' + $th->getMessage()], 500);
        }
    }



    /**
     * Reset Password
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     *
     */
    public function resetPassword(Request $request)
    {
        //ensure email exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User with this email not found'], 404);
        }

        //validate Otp
        $otp = (new Otp)->validate($user->email, $request->otp);

        if (!$otp->status) {
            return response()->json(['message' => 'Otp is not valid'], 401);
        }

        //update password
        $user->update(['password' => bcrypt($request->password)]);

        return response()->json(['message' => 'Password updated successfully'], 200);
    }
}
