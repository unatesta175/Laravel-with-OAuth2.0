<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Passport\Token;


class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'role' => 'in:client,therapist,admin'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $request->role ?? 'client',
            'email_verified_at' => now(), // Auto-verify for demo
        ]);

        // Create Passport token with user scopes
        $scopes = $user->getOAuthScopes();
        $tokenResult = $user->createToken('SPA Token', $scopes);
        $token = $tokenResult->token;

        // Create HTTP-only cookie
        $cookie = cookie(
            name: 'access_token',
            value: $token->id,
            minutes: 120, // 2 hours
            path: '/',
            domain: null,
            secure: app()->environment('production'),
            httpOnly: true,
            raw: false,
            sameSite: 'strict'
        );

        return response()->json([
            'message' => 'User registered successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->phone,
                'image' => $user->image,
            ],
            'authenticated' => true,
        ])->cookie($cookie);
    }

    /**
     * Login user
     */
    public function login(Request $request): JsonResponse
    {


        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Revoke all existing tokens for this user
        $user->tokens()->delete();

        // Create new Passport token
        $scopes = $user->getOAuthScopes();
        $tokenResult = $user->createToken('SPA Token', $scopes);
        $token = $tokenResult->token;

        // Create HTTP-only cookie
        $cookie = cookie(
            name: 'access_token',
            value: $token->id,
            minutes: 120, // 2 hours
            path: '/',
            domain: null,
            secure: app()->environment('production'),
            httpOnly: true,
            raw: false,
            sameSite: 'strict'
        );

        return response()->json([
            'message' => 'You have successfully logged in!',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->phone,
                'image' => $user->image,
            ],
            'authenticated' => true,
        ])->cookie($cookie);
        }



    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        // Get the token from request attributes (set by middleware)
        $token = $request->attributes->get('passport_token');

        if ($token) {
            // Revoke the token
            $token->revoke();
        }

        // Clear the HTTP-only cookie
        $cookie = cookie()->forget('access_token');

        return response()->json([
            'message' => 'Logged out successfully!'
        ])->cookie($cookie);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->phone,
                'image' => $user->image,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
            ]
        ]);
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        $currentToken = $request->attributes->get('passport_token');

        if ($currentToken) {
            // Revoke current token
            $currentToken->revoke();
        }

        // Create new token
        $scopes = $user->getOAuthScopes();
        $tokenResult = $user->createToken('SPA Token', $scopes);
        $token = $tokenResult->token;

        // Create new HTTP-only cookie
        $cookie = cookie(
            name: 'access_token',
            value: $token->id,
            minutes: 120, // 2 hours
            path: '/',
            domain: null,
            secure: app()->environment('production'),
            httpOnly: true,
            raw: false,
            sameSite: 'strict'
        );

        return response()->json([
            'message' => 'Token refreshed successfully',
            'authenticated' => true,
        ])->cookie($cookie);
    }


    /**
     * Update user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        Log::info('Update profile method called', [
            'method' => $request->method(),
            'url' => $request->url(),
            'user_id' => $request->user()?->id
        ]);

        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'password' => 'sometimes|required|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = [];

            if ($request->has('name')) {
                $updateData['name'] = $request->name;
            }

            if ($request->has('phone')) {
                $updateData['phone'] = $request->phone;
            }

            if ($request->has('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();

                // Save to storage/app/public/profile_images
                $storagePath = $image->storeAs('profile_images', $imageName, 'public');

                // Generate URL accessible from frontend
                $imageUrl = '/storage/' . $storagePath;
                $updateData['image'] = $imageUrl;
            }

            $user->update($updateData);

            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'phone' => $user->phone,
                    'image' => $user->image,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Profile update error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update profile'
            ], 500);
        }
    }

    /**
     * Change password (separate from profile update)
     */
    public function changePassword(Request $request): JsonResponse
    {
        Log::info('Change password method called', [
            'method' => $request->method(),
            'url' => $request->url(),
            'user_id' => $request->user()?->id
        ]);

        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect',
                'errors' => ['current_password' => ['The current password is incorrect']]
            ], 422);
        }

        try {
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'message' => 'Password changed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Password change error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to change password'
            ], 500);
        }
    }

    /**
     * Google OAuth login
     */
    public function googleLogin(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|email',
            'name' => 'required|string',
            'google_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // For now, we'll simulate Google OAuth verification
            // In production, you would verify the Google token here
            $email = $request->input('email');
            $name = $request->input('name');
            $googleId = $request->input('google_id');
            $avatar = $request->input('avatar');

            if (!$email || !$name) {
                return response()->json([
                    'message' => 'Invalid Google token data'
                ], 401);
            }

            // Find or create user
            $user = User::where('email', $email)->first();

            if (!$user) {
                // Create new user with Google avatar
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make(Str::random(32)), // Random password for Google users
                    'role' => 'client',
                    'email_verified_at' => now(),
                    'google_id' => $googleId,
                    'image' => $avatar, // Save Google avatar URL
                ]);
            } else {
                // Update existing user with Google info if not already set
                $updateData = [];
                if (!$user->google_id) {
                    $updateData['google_id'] = $googleId;
                }
                if (!$user->image && $avatar) {
                    $updateData['image'] = $avatar;
                }
                if (!empty($updateData)) {
                    $user->update($updateData);
                }
            }

            // Revoke existing tokens
            $user->tokens()->delete();

            // Create new token
            $scopes = $user->getOAuthScopes();
            $tokenResult = $user->createToken('SPA Token', $scopes);
            $token = $tokenResult->token;

            // Create HTTP-only cookie
            $cookie = cookie(
                name: 'access_token',
                value: $token->id,
                minutes: 120,
                path: '/',
                domain: null,
                secure: app()->environment('production'),
                httpOnly: true,
                raw: false,
                sameSite: 'strict'
            );

            return response()->json([
                'message' => 'Google login successful!',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'phone' => $user->phone,
                    'image' => $user->image, // This will now contain the Google avatar URL
                ],
                'authenticated' => true,
            ])->cookie($cookie);

        } catch (\Exception $e) {
            Log::error('Google OAuth error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Google authentication failed'
            ], 500);
        }
    }

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'No user found with this email address'
                ], 404);
            }

            // Generate reset token
            $token = Str::random(64);

            // Store token in database
            $user->update([
                'password_reset_token' => $token,
                'password_reset_expires' => now()->addHours(1),
            ]);

            // Create reset URL for frontend
            $resetUrl = config('app.frontend_url', 'http://localhost:3000') . '/auth/reset-password?token=' . $token . '&email=' . urlencode($request->email);

            // Send password reset email
            \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($user, $resetUrl) {
                $message->to($user->email, $user->name)
                       ->subject('Password Reset Request - ' . config('app.name'))
                       ->html("
                           <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                               <h2 style='color: #333; text-align: center;'>Password Reset Request</h2>
                               <p>Hello {$user->name},</p>
                               <p>You have requested to reset your password. Click the button below to reset your password:</p>
                               <div style='text-align: center; margin: 30px 0;'>
                                   <a href='{$resetUrl}' style='background-color: #007bff; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>Reset Password</a>
                               </div>
                               <p>Or copy and paste this link in your browser:</p>
                               <p style='word-break: break-all; color: #007bff;'>{$resetUrl}</p>
                               <p><strong>This link will expire in 1 hour.</strong></p>
                               <p>If you did not request this password reset, please ignore this email.</p>
                               <br>
                               <p>Best regards,<br>" . config('app.name') . " Team</p>
                           </div>
                       ");
            });

            return response()->json([
                'message' => 'Password reset email sent successfully! Please check your inbox.'
            ]);

        } catch (\Exception $e) {
            Log::error('Password reset email error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to send password reset email'
            ], 500);
        }
    }

    /**
     * Reset password with token
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)
                       ->where('password_reset_token', $request->token)
                       ->where('password_reset_expires', '>', now())
                       ->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Invalid or expired reset token'
                ], 400);
            }

            // Update password and clear reset token
            $user->update([
                'password' => Hash::make($request->password),
                'password_reset_token' => null,
                'password_reset_expires' => null,
            ]);

            return response()->json([
                'message' => 'Password reset successfully! You can now login with your new password.'
            ]);

        } catch (\Exception $e) {
            Log::error('Password reset error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to reset password'
            ], 500);
        }
    }
}
