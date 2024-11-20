<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Http\Requests\StoreBalanceRequest;
use App\Http\Requests\UpdateBalanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getAllBalances()
    {
        $manager = Auth::guard('manager')->user();
        $user = Auth::guard('user')->user();
        if($manager){
            $balances = Balance::where('manager_id', $manager->id)->get();
            $totalBalance = $balances->sum('balance');
            return response()->json(['balances' => $balances,'totalBalance' => $totalBalance], 200);
        }
        if($user){
            $balances = Balance::where('manager_id', $user->manager_id)->get();
            $totalBalance = $balances->sum('balance');
            return response()->json(['balances' => $balances,'totalBalance' => $totalBalance], 200);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    public function getBalanceByMonth ($month){
        $manager = Auth::guard('manager')->user();
        $user = Auth::guard('user')->user();
        if($month == 'current'){
            $currentMonth = now()->month;
            $currentYear = now()->year;

            if ($manager) {
                $balances = Balance::where('manager_id', $manager->id)
                    ->whereMonth('created_at', $currentMonth)
                    ->whereYear('created_at', $currentYear)
                    ->get();
            } elseif ($user) {
                $balances = Balance::where('manager_id', $user->manager_id)
                    ->whereMonth('created_at', $currentMonth)
                    ->whereYear('created_at', $currentYear)
                    ->get();
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $totalBalance = $balances->sum('balance');
            return response()->json(['balances' => $balances, 'totalBalance' => $totalBalance], 200);
        }
        if($month == 'previous'){
            $previousMonth = now()->subMonth()->month;
            $previousYear = now()->subMonth()->year;

            if ($manager) {
                $balances = Balance::where('manager_id', $manager->id)
                    ->whereMonth('created_at', $previousMonth)
                    ->whereYear('created_at', $previousYear)
                    ->get();
            } elseif ($user) {
                $balances = Balance::where('manager_id', $user->manager_id)
                    ->whereMonth('created_at', $previousMonth)
                    ->whereYear('created_at', $previousYear)
                    ->get();
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $totalBalance = $balances->sum('balance');
            return response()->json(['balances' => $balances, 'totalBalance' => $totalBalance], 200);
        }
        return response()->json(['error' => 'Invalid month'], 400);


    }
    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBalanceRequest $request)
    {
    $validated = $request->validated();
    $user = \App\Models\User::find($validated['user_id']);
    if(!$user){
        return response()->json(['message' => 'User not found'], 404);
    }
    $manager = auth()->guard('manager')->user();
    if(!$user || !$manager){
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    Balance::create([
        'user_id' => $user->id,
        'manager_id' => $manager->id,
        'balance' => $validated['balance'],
        'user_name' => $user->name,
    ]);
    return response()->json(['message' => 'Balance added successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Balance $balance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Balance $balance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBalanceRequest $request, Balance $balance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,$id)
    {
        $manager = auth()->guard('manager')->user();

        // Check if the expense exists and belongs to the manager
        $balance = Balance::where('id', $id)->where('manager_id', $manager->id)->first();

        if (!$balance) {
            return response()->json(['error' => 'balance not found '], 404);
        }

        // Delete the expense
        $balance->delete();

        return response()->json(['message' => 'balance deleted successfully'], 200);
    }
}
