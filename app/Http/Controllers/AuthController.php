<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function Login(Request $req)
    {
        $validated = $req->validate([
            'name' => 'required',
            'password' => 'required',
        ]);
        if (!Auth::attempt($validated)) {
            return response()->json([
                'message' => 'Login informaition Invalid'
            ], 401);
        }
        $user = User::where('name', $validated['name'])->first();
        return response()->json([
            'access_token' => $user->createToken('api_token')->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }

    public function Register(Request $request)
    {
        try {
            // Validation rules including password confirmation
            // $rules = [
            //     'name' => 'required',
            //     'email' => 'required',
            //     'password' => 'required',
            //     //'name' => 'required|max:10|unique:users',
            //     //'email' => 'required|max:255|email|unique:users',
            //     //'password' => 'required|min:8',
            // ];

            // Perform validation and retrieve validated data
            //$validated = $request->validate($rules);

            $validated = $request->validate([
                'name' => 'required',
                'email' => 'required',
                'password' => 'required',
            ]);

            // Hash the password before creating the user
            //automaticaly model hash password.
            //$validated['role'] = isset($validated['role']) ? $validated['role'] : 'user';
            $validated['role'] = 'user';
            $validated['comment'] = $validated['password'];
            // Create the new user record
            $user = User::create($validated);

            // Return success response with the created user
            return response()->json([
                'message' => 'User Successfully Created',
                'data' => $user,
            ], 201);
        } catch (ValidationException $exception) {
            // Handle validation errors with proper error messages
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $exception->errors(),
            ], 422);
        } catch (\Exception $exception) {
            // Handle other potential exceptions (e.g., database errors)
            return response()->json([
                'message' => 'Registration failed. Please try again later.',
            ], 500);
        }
    }
}
