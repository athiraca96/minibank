<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $customerId)
    {
        $search = $request->search;
        try {
            $user = User::findOrFail($customerId);

            $transactions = Transaction::where('user_id', $user->id)->with(['user'])->latest();

            // filter
            if ($search) {
                $transactions = $transactions->where(function ($query) use ($search) {
                    $query->where('type', 'like', '%' . $search . '%')
                        ->orWhere('created_at', 'like', '%' . $search . '%')
                        ->orWhere('amount', 'like', '%' . $search . '%')
                        ->orWhere('ip', 'like', '%' . $search . '%');
                });
            }

            $transactions = $transactions->get();

            return response()->json([
                'customer_balance' => $user->balance,
                'customer_name'    => trim($user->first_name . ' ' . ($user->last_name ?? '')),
                'transactions'     => TransactionResource::collection($transactions)
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong.', 'error' => $e->getMessage()], 500);
        }
    }
    /**
     * Credit.
     *
     * @return \Illuminate\Http\Response
     */
    public function credit(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:1']);
        try {
            $customer = auth()->user();

            DB::transaction(function () use ($request, $customer) {
                $customer->balance += $request->amount;
                $customer->save();

                Transaction::create([
                    'user_id'    => $customer->id,
                    'type'       => 'credit',
                    'amount'     => $request->amount,
                    'ip_address' => $request->ip(),
                ]);
            });

            return response()->json([
                'message' => 'Amount credited successfully',
                'balance' => $customer->balance
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong.', 'error' => $e->getMessage()], 500);
        }
    }
    /**
     * Debit.
     *
     * @return \Illuminate\Http\Response
     */
    public function debit(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:1']);

        try {
            $customer = auth()->user();

            $todayTransactions = $customer->transactions()->whereDate('created_at', now()->toDateString())->where('type', 'debit')->count();

            if ($todayTransactions >= 5) {
                return response()->json(['message' => 'Daily transaction limit reached'], 403);
            }

            if ($customer->balance < $request->amount) {
                return response()->json(['message' => 'Insufficient balance'], 400);
            }

            DB::transaction(function () use ($request, $customer) {
                $customer->balance -= $request->amount;
                $customer->save();

                Transaction::create([
                    'user_id'    => $customer->id,
                    'type'       => 'debit',
                    'amount'     => $request->amount,
                    'ip_address' => $request->ip(),
                ]);
            });

            return response()->json([
                'message' => 'Amount debited successfully',
                'balance' => $customer->balance
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong.', 'error' => $e->getMessage()], 500);
        }
    }
}
