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
    public function userSearch(Request $request)

    {
        $manager = Auth::guard('manager')->user();
        $query = $request->input('query');
        $request->validate([
            'query' => 'required|string|min:2',
        ]);
        $user = User::where('manager_id', $manager->id)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->paginate(10);
        return response()->json($user);
    }
    public function userSearchapp(Request $request ,$query)
    {
        $manager = Auth::guard('manager')->user();
        if (!$manager) {
            return response()->json(['error' => 'Unauthorized access'], 401);
        }
        if (!$query) {
            return response()->json(['error' => 'Search query is required'], 400);
        }

        $user = User::where('manager_id', $manager->id)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->paginate(10);
        return response()->json($user);


    }
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

        $validator = validator()->make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);
        if(!$validator->fails()){
            $validatedData = $validator->validated();
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => bcrypt($validatedData['password']),
                'manager_id' => $manager->id,

            ]);
            return response()->json(['message' => 'User account created successfully', 'user' => $user], 201);
        }else {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ]);
        }

        // Create the new user account



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
            return response()->json([
                'name' => $user->name,
                'id' => $user->id,
                'email' => $user->email,
                'role' => 'user',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],200);
    }
        return response()->json(['error' => 'Unauthorized.'], 401);

}
public function logoutUser  (Request $request)
{
    $user = Auth::guard('user')->user();
    if ($user) {
        $user->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
    return response()->json(['error' => 'Unauthorized'], 401);
}
public function deleteUser(Request $request,$userId){
        $manager = Auth::guard('manager')->user();
        if (!$manager) {
            return response()->json(['error' => 'Unauthorized. Only managers can delete user accounts.'], 403);
        }
        $user = User::where('id', $userId)->where('manager_id', $manager->id)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User account deleted successfully'], 200);
}
}

