<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle a registration request.
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // Casted as 'hashed' in model
            'role' => 'employee',
            'is_email_verified' => false,
            'onboarding_status' => 'pending',
        ]);

        // Log the user in
        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ], 201);
    }

    /**
     * Get the authenticated user's account details.
     */
    public function account(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'is_email_verified' => $user->is_email_verified,
            'onboarding_status' => $user->onboarding_status,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    }
    /**
     * Send a password reset OTP to the user's email.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Always return success to prevent email enumeration
        if (! $user) {
            return response()->json([
                'message' => 'If an account exists with that email, a reset code has been sent.',
            ]);
        }

        // Generate password reset OTP
        $result = Otp::generate($user, 'password_reset', 6, 10);

        // In production, send email here. For dev, return the code in response.
        return response()->json([
            'message' => 'If an account exists with that email, a reset code has been sent.',
            'code' => $result['code'], // Remove this line in production
        ]);
    }

    /**
     * Reset the user's password using an OTP code.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json([
                'message' => 'Invalid reset code.',
            ], 422);
        }

        $verified = Otp::verify($user, $request->code, 'password_reset');

        if (! $verified) {
            return response()->json([
                'message' => 'Invalid or expired reset code.',
            ], 422);
        }

        // Update the password (cast as 'hashed' in model)
        $user->update(['password' => $request->password]);

        return response()->json([
            'message' => 'Password has been reset successfully.',
        ]);
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            if ($user) {
                LoginLog::logFailure($user, 'invalid_credentials', $request->ip(), $request->userAgent());
            }

            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        // Log successful login
        LoginLog::logSuccess($user, $request->ip(), $request->userAgent());

        // Log the user in via session
        Auth::login($user);

        $request->session()->regenerate();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    /**
     * Handle a logout request.
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Get the authenticated user.
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]);
    }
}
