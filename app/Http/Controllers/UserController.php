<?php

namespace App\Http\Controllers;

use App\Models\Manager;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //

    /**
     * Create a new user account.
     *
     *
     */
    public function getAllUsers()
    {
        $manager = Auth::guard('manager')->user();
        $user = Auth::guard('user')->user();

        if ($manager) {
            $users = User::where('manager_id', $manager->id)->get();
            return response()->json(['users' => $users], 200);
        }

        if ($user) {
            // Ensure the user can only see users managed by their manager
            $users = User::where('manager_id', $user->manager_id)->get();
            return response()->json(['users' => $users], 200);
        }

        return response()->json(['error' => 'Unauthorized. Authentication required.'], 401);
    }
    public function createUser(Request $request)
    {
        // Check user is a manager
        $manager = Auth::guard('manager')->user();

        if (!$manager) {
            return response()->json(['error' => 'Unauthorized. Only managers can create new user accounts.'], 403);
        }

        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',

        ]);

        // Create the new user account
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'manager_id' => $manager->id,

        ]);

        return response()->json(['message' => 'User account created successfully', 'user' => $user], 201);
    }
    public function loginUser(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);
        // Attempt to authenticate the user
        if(Auth::attempt(['email'=>$validatedData['email'],'password'=>$validatedData['password']])){
            $user = Auth::user();
            $token= $user->createToken('MyApp')->plainTextToken;
            return response()->json(['token'=>$token],200);
    }
        return response()->json(['error' => 'Unauthorized.'], 403);

}}
