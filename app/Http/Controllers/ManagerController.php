<?php

namespace App\Http\Controllers;

use App\Models\Manager;
use App\Http\Requests\StoreManagerRequest;
use App\Http\Requests\UpdateManagerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Str;

class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $manager = Auth::guard('manager')->user();
        $user = Auth::guard('user')->user();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Adjusted the foreign key to 'user_id'
       if($manager){
           $allInfo = Manager::with(['expenses' => function ($query) use ($manager, $currentMonth, $currentYear) {
               $query->where('manager_id', $manager->id)
                   ->whereMonth('date', $currentMonth)
                   ->whereYear('date', $currentYear);
           },'balance' => function ($query) use ($manager, $currentMonth, $currentYear) {
               $query->where('manager_id', $manager->id)
                   ->whereMonth('created_at', $currentMonth)
                   ->whereYear('created_at', $currentYear);
           },'users'])->findOrFail($manager->id);
       }
       if($user){
           $allInfo = Manager::with(['expenses' => function ($query) use ($user, $currentMonth, $currentYear) {
               $query->where('manager_id',$user->manager_id)
                   ->whereMonth('date', $currentMonth)
                   ->whereYear('date', $currentYear);
           },'balance' => function ($query) use ($user, $currentMonth, $currentYear) {
               $query->where('manager_id', $user->manager_id)
                   ->whereMonth('created_at', $currentMonth)
                   ->whereYear('created_at', $currentYear);
           },'users'])->findOrFail($user->manager_id);
       }
        $totalExpenses = $allInfo->expenses->sum('amount');
        $totalBalance = $allInfo->balance->sum('balance');

        $currentBalance = $totalBalance - $totalExpenses;


        return response()->json([
            'manager' => [
                'id' => $allInfo->id,
                'name' => $allInfo->name,
                'email' => $allInfo->email,
                'meal_name' => $allInfo->meal_name,

            ],
            'users' => $allInfo->users,
            'expenses' => $allInfo->expenses,
            'balance' => $allInfo->balance,
            'totalExpenses' => $totalExpenses,
            'totalBalance' => $totalBalance,
            'currentBalance' => $currentBalance,

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function login(Request $request)
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
                'name' => $manager->name,
                'meal_name' => $manager->meal_name,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }

        //  return an error response
        return response()->json([
            'message' => 'Invalid login credentials',

        ], 401);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreManagerRequest $request)
    {
        // Validate the request data
        $validator = validator()->make($request->all(), [
            'name' => 'required|string|max:255|unique:managers',
            'meal_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:managers',
            'password' => 'required|string|min:8',
        ]);

        if(!$validator->fails()){
            $validatedData = $validator->validated();

            $manager = Manager::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'meal_name' => $validatedData['meal_name'],
                'password' => Hash::make($validatedData['password']),
            ]);

            return response()->json([
                'message' => 'success',
                'manager' => $manager
            ], 201);
        }else {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ]);
        }
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
    public function logoutManager(Request $request)
    {
        $manager = Auth::guard('manager')->user();
        if ($manager) {
            $manager->tokens()->delete();
            return response()->json(['message' => 'Logged out successfully'], 200);
        }
        return response()->json(['error' => 'unauthorized'], 401);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);


        $manager = Manager::where('email', $request->email)->first();

        if (!$manager) {
            return response()->json(['error' => 'We can\'t find a manager with that email address.'], 404);
        }

        $token = Str::random(60);
        $manager->update(['reset_token' => $token]);

        // Send email using Mailtrap
        Mail::to($manager->email)->send(new ResetPasswordMail($token));

        return response()->json(['message' => 'We have emailed your password reset token!'], 200);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $manager = Manager::where('email', $request->email)
                          ->where('reset_token', $request->token)
                          ->first();

        if (!$manager) {
            return response()->json(['error' => 'This password reset token is invalid.'], 400);
        }

        $manager->password = Hash::make($request->password);
        $manager->reset_token = null;
        $manager->save();

        return response()->json(['message' => 'Your password has been reset!'], 200);
    }
}
