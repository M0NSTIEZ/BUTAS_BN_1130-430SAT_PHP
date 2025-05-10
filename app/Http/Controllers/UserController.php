<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    // Login method
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    // Signup method
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,owner,renter', // Acceptable roles
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Signup successful',
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    // Logout method
    public function logout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        // Revoke only the current user's token
        $user->tokens()->where('id', $request->user()->currentAccessToken()->id)->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    // Get all users (Admin only)
    public function index()
    {
        // Check if the user has permission to view all users
        if (Gate::denies('view-all-users')) {
            abort(403, 'You are not authorized to view all users.');
        }

        // Logic to fetch and return all users
        $users = User::all();
        return response()->json($users);
    }

    // Update user info (Authenticated users can update their own profile)
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        // Ensure user can only update their own profile
        if ($user->id !== (int)$id) {
            return response()->json(['error' => 'You cannot update another user\'s information'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',  // Allow password update
            // You can add other fields as needed
        ]);

        // Only update password if it's provided
        if ($request->filled('password')) {
            $request->merge(['password' => Hash::make($request->password)]);
        }

        $user->update($request->only(['name', 'email', 'password']));

        return response()->json(['message' => 'User updated successfully']);
    }

    // Delete user (Admin only)
    public function destroy($id)
    {
        // Check if the user has permission to delete a user (only admin can delete)
        if (Gate::denies('admin')) {
            return response()->json(['message' => 'You are not authorized to delete this user.'], 403);
        }

        // Find the user and delete it
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
