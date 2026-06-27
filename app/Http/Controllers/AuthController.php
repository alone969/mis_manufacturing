<?php

namespace App\Http\Controllers;

use App\Models\DeviceLog;
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

        $user->assignRole('employee');

        Auth::login($user);
        $request->session()->regenerate();

        DeviceLog::recordLogin($user, $request->ip(), $request->userAgent());
        $this->logActivity('register', 'New user registered');

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
            'settings' => $user->settings,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only(['name', 'email']));

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    /**
     * Change the authenticated user's password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'The current password is incorrect.',
            ], 422);
        }

        $user->update(['password' => $request->password]);

        $this->logActivity('password_change', 'Password changed');

        return response()->json([
            'message' => 'Password changed successfully.',
        ]);
    }

    /**
     * Update the authenticated user's settings/preferences.
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'settings' => 'required|array',
            'settings.language' => 'sometimes|string|in:en,es,fr,de,ar',
            'settings.email_notifications' => 'sometimes|boolean',
            'settings.shift_reminders' => 'sometimes|boolean',
            'settings.theme' => 'sometimes|string|in:light,dark,system',
        ]);

        $currentSettings = $user->settings ?? [];
        $newSettings = array_merge($currentSettings, $request->settings);

        $user->update(['settings' => $newSettings]);

        return response()->json([
            'message' => 'Settings updated successfully.',
            'settings' => $newSettings,
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

        $result = Otp::generate($user, 'password_reset', 6, 10);

        // TODO: In production, send email with the code via a mailer.
        return response()->json([
            'message' => 'If an account exists with that email, a reset code has been sent.',
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

        $user->update(['password' => $request->password]);

        $this->logActivity('password_reset', 'Password reset via OTP');

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

        LoginLog::logSuccess($user, $request->ip(), $request->userAgent());

        Auth::login($user);
        $request->session()->regenerate();

        DeviceLog::recordLogin($user, $request->ip(), $request->userAgent());
        $this->logActivity('login', 'User logged in');

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
        $this->logActivity('logout', 'User logged out');

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

    /**
     * Send email verification OTP.
     */
    public function sendVerificationOtp(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->is_email_verified) {
            return response()->json([
                'message' => 'Email is already verified.',
            ]);
        }

        $result = Otp::generate($user, 'email_verification', 6, 15);

        // TODO: In production, send email with the code.
        return response()->json([
            'message' => 'Verification code has been sent to your email.',
        ]);
    }

    /**
     * Verify email with OTP code.
     */
    public function verifyEmail(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = $request->user();

        $verified = Otp::verify($user, $request->code, 'email_verification');

        if (! $verified) {
            return response()->json([
                'message' => 'Invalid or expired verification code.',
            ], 422);
        }

        $user->update(['is_email_verified' => true]);

        $this->logActivity('email_verified', 'Email verified successfully');

        return response()->json([
            'message' => 'Email verified successfully.',
        ]);
    }

    /**
     * Send login OTP.
     */
    public function sendLoginOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json([
                'message' => 'If an account exists with that email, a login code has been sent.',
            ]);
        }

        $result = Otp::generate($user, 'login', 6, 5);

        // TODO: In production, send email with the code.
        return response()->json([
            'message' => 'If an account exists with that email, a login code has been sent.',
        ]);
    }

    /**
     * Login with OTP code.
     */
    public function loginWithOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json([
                'message' => 'Invalid login code.',
            ], 422);
        }

        $verified = Otp::verify($user, $request->code, 'login');

        if (! $verified) {
            return response()->json([
                'message' => 'Invalid or expired login code.',
            ], 422);
        }

        LoginLog::logSuccess($user, $request->ip(), $request->userAgent());

        Auth::login($user);
        $request->session()->regenerate();

        DeviceLog::recordLogin($user, $request->ip(), $request->userAgent());
        $this->logActivity('login', 'User logged in via OTP');

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }
}
