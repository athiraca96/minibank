<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomerRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $customers = User::withCount('transactions')->latest();

        // filter
        if ($search) {
            $customers = $customers->where(function ($query) use ($search) {
                $query->where('customer_id', 'like', '%' . $search . '%')
                    ->orWhere('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('balance', 'like', '%' . $search . '%');
            });
        }

        $customers = $customers->get();

        return (UserResource::collection($customers));
    }

    /**
     * Create Customer
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return Response
     */
    public function store(CreateCustomerRequest $request)
    {
        $input = $request->validated();
        try {
            // Get last customer ID and generate a new one
            $lastId = User::max('customer_id'); // Fetch the highest customer_id
            $newCustomerId = 'C' . (isset($lastId) ? (intval(substr($lastId, 1)) + 1) : 1001);
            $input['customer_id'] = $newCustomerId;

            User::create($input);

            return response()->json(['message' => 'Customer Created Successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong.', 'error' => $e->getMessage()], 500);
        }
    }
}
