<?php

namespace App\Http\Controllers;

use App\Models\Manager;
use App\Http\Requests\StoreManagerRequest;
use App\Http\Requests\UpdateManagerRequest;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function login()
    {
        // Validate the login credentials
        $credentials = request()->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find  manager by email
        $manager = Manager::where('email', $credentials['email'])->first();

        // Check if manager exists and password is correct
        if ($manager && Hash::check($credentials['password'], $manager->password)) {
            // Authentication successful
            $token = $manager->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Manager logged in successfully',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }

        //  return an error response
        return response()->json([
            'message' => 'Invalid login credentials',
            'errors' => [
                'email' => ['These credentials do not match our records.'],
                'password' => ['These credentials do not match our records.']
            ]
        ], 401);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreManagerRequest $request)
    {
        // Validate the request data
        $validatedData = $request->validated();

        // Create a new manager
        $manager = Manager::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'meal_name' => $validatedData['meal_name'],
            'password' => bcrypt($validatedData['password']),
        ]);



        // Return a response
        return response()->json([
            'message' => 'Manager registered successfully',
            'manager' => $manager
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Manager $manager)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Manager $manager)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateManagerRequest $request, Manager $manager)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Manager $manager)
    {
        //
    }
}
