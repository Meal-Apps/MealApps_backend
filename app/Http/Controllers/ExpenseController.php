<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
            $expenses = Expense::where('manager_id', $manager->id)
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->get();
        } elseif ($user) {
            $expenses = Expense::where('manager_id', $user->manager_id)
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
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
                ->whereMonth('date', $previousMonth)
                ->whereYear('date', $previousYear)
                ->get();
        } elseif ($user) {
            $expenses = Expense::where('manager_id', $user->manager_id)
                ->whereMonth('date', $previousMonth)
                ->whereYear('date', $previousYear)
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
            'manager_id' => $manager->id,
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
    public function destroy(Request $request, $id)
    {
        $manager = auth()->guard('manager')->user();

        // Check if the expense exists and belongs to the manager
        $expense = Expense::where('id', $id)->where('manager_id', $manager->id)->first();

        if (!$expense) {
            return response()->json(['error' => 'Expense not found '], 404);
        }

        // Delete the expense
        $expense->delete();

        return response()->json(['message' => 'Expense deleted successfully'], 200);
    }
}
