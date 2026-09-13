<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        try {
            \Log::info('Login attempt initiated');
            
            $validated = $request->validate([
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
            ]);

            \Log::info('Validation passed');

            $user = User::where('email', $validated['email'])->first();

            if (!$user) {
                \Log::warning('Login failed: user not found');
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                ], 401);
            }

            if (!Hash::check($validated['password'], $user->password)) {
                \Log::warning('Login failed: invalid password');
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                ], 401);
            }

            \Log::info('User authenticated, creating token', ['user_id' => $user->id]);

            try {
                $token = $user->createToken('auth-token')->plainTextToken;
                \Log::info('Token created successfully', ['user_id' => $user->id]);
            } catch (\Exception $tokenError) {
                \Log::error('Token creation failed', [
                    'user_id' => $user->id,
                    'error' => $tokenError->getMessage(),
                    'trace' => $tokenError->getTraceAsString(),
                ]);
                throw $tokenError;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ],
                'message' => 'Login successful',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Validation error', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Login error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => ['file' => $e->getFile(), 'line' => $e->getLine()],
            ], 500);
        }
    }
}