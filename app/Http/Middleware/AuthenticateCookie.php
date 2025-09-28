<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Passport\Token;
use Laravel\Passport\TokenRepository;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthenticateCookie
{
    public function handle(Request $request, Closure $next)
    {
        // Get token from HTTP-only cookie
        $tokenValue = $request->cookie('access_token');
        
        if (!$tokenValue) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        try {
            // Find the token in the database
            $token = Token::where('id', $tokenValue)->first();

            if (!$token || $token->revoked || $token->expires_at->isPast()) {
                return response()->json(['message' => 'Token expired or invalid'], 401);
            }

            // Get the user associated with the token
            $user = User::find($token->user_id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 401);
            }

            // Set the authenticated user for the request
            Auth::setUser($user);
            $request->setUserResolver(function () use ($user) {
                return $user;
            });

            // Add token to request for potential revocation
            $request->attributes->set('passport_token', $token);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Authentication failed'], 401);
        }

        return $next($request);
    }
}
