<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $users = User::select('id', 'firstname', 'lastname', 'email', 'phone', 'role')->get();

        return response()->json([
            'success' => true,
            'status' => 200,
            'users' => $users,
        ]);
    }

    public function setRole(Request $request)
    {
        $admin = Auth::user();

        if ($admin->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $user = User::where('id', $request->user_id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot change role of another admin'
            ], 403);
        }

        $user->role = $request->role;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User role updated successfully'
        ]);
    }

    public function updateProfile(Request $request, User $user)
    {
        $request->validate([
            'firstname'  => 'required|string|max:255',
            'lastname'   => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id,
        ]);

        if (auth()->user()->role !== 'admin' && auth()->id() !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $user->update([
            'firstname' => $request->firstname,
            'lastname'  => $request->lastname,
            'email'     => $request->email,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User profile updated successfully',
            'user' => $user
        ]);
    }

    public function destroy(User $user)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}
