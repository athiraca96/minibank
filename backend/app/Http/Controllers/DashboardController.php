<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics.
     *
     * @return JsonResponse
     */
    public function index()
    {
        try {
            return response()->json([
                'total_users'  => User::count(),
                'total_revenue' => Transaction::where('type', 'credit')->sum('amount'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'error' => $e->getMessage()], 500);
        }
    }
}
