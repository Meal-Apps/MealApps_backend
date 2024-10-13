<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Http\Requests\StoreBalanceRequest;
use App\Http\Requests\UpdateBalanceRequest;

class BalanceController extends Controller
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBalanceRequest $request,$userID)
    {
    $validated = $request->validated();
    $user = \App\Models\User::find($userID);
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
    public function destroy(Balance $balance)
    {
        //
    }
}
