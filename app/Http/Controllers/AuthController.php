<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
       //TODO : Implement login functionality
       
       // Validate the incoming request
       $validated = $request->validate([
           'email' => 'required|email',
           'password' => 'required|string|min:6'
       ]);

       // Attempt to authenticate the user
       if (Auth::attempt($validated)) {
           $user = Auth::user();
           
           // Generate API token for the authenticated user
           $token = $user->createToken('auth_token')->plainTextToken;
           
           // Log successful login
           Log::info('User login successful', ['user_id' => $user->id, 'email' => $user->email]);
           
           // Return success response with token and user data
           return response()->json([
               'message' => 'Login successful',
               'token' => $token,
               'user' => [
                   'id' => $user->id,
                   'first_name' => $user->first_name,
                   'last_name' => $user->last_name,
                   'email' => $user->email,
                   'role' => $user->role
               ]
           ], 200);
       }
       
       // Log failed login attempt
       Log::warning('Failed login attempt', ['email' => $validated['email']]);
       
       // Return error response for invalid credentials
       return response()->json([
           'message' => 'Invalid email or password'
       ], 401);
    }

    
}
