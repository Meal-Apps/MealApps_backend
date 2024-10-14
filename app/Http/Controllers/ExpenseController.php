<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
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
    public function getExpensesByMonth($month)
    {
        $manager = Auth::guard('manager')->user();
        $user = Auth::guard('user')->user();

       if($month == 'current'){
        $currentMonth = now()->month;
        $currentYear = now()->year;

        if ($manager) {
            $expenses = Expense::where('user_id', $manager->id)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->get();
        } elseif ($user) {
            $expenses = Expense::where('user_id', $user->manager_id)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->get();
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $totalExpenses = $expenses->sum('amount');
        return response()->json(['expenses' => $expenses, 'totalExpenses' => $totalExpenses], 200);
       }
       if($month == 'previous'){
        $previousMonth = now()->subMonth()->month;
        $previousYear = now()->subMonth()->year;

        if ($manager) {
            $expenses = Expense::where('manager_id', $manager->id)
                ->whereMonth('created_at', $previousMonth)
                ->whereYear('created_at', $previousYear)
                ->get();
        } elseif ($user) {
            $expenses = Expense::where('manager_id', $user->manager_id)
                ->whereMonth('created_at', $previousMonth)
                ->whereYear('created_at', $previousYear)
                ->get();
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $totalExpenses = $expenses->sum('amount');
        return response()->json(['expenses' => $expenses, 'totalExpenses' => $totalExpenses], 200);
       }
       return response()->json(['error' => 'Invalid month'], 400);

    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExpenseRequest $request)
    {
        $validated = $request->validated();
        //get the manager
        $manager = auth()->guard('manager')->user();

       $expense = Expense::create([
            'user_id' => $manager->id,
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'date' => $validated['date'],

        ]);

        return response()->json(['message' => 'Expense added successfully','expense' => $expense], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        //
    }
}
