<?php

namespace App\Http\Controllers;

use App\Jobs\Admin\AdminWithdrawalInitiated;
use App\Jobs\User\UserWithdrawalInitiated;
use App\Jobs\User\UserWithdrawalProcessed;
use App\Models\Withdrawal;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $withdrawals = Withdrawal::latest()->paginate(10);

            return response()->json(['withdrawals' => $withdrawals]);
        } catch (\Throwable $th) {
            Log::error("List all Withdrawals for Admin [Pagination]" . "===" . $th->getMessage());
        }
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function userIndex()
    {
        try {
            $withdrawals = Withdrawal::where([[
                'user_id', auth()->id(),
            ]])->latest()->paginate(10);

            return response()->json(['withdrawals' => $withdrawals]);
        } catch (\Throwable $th) {
            Log::error("List all Withdrawals for a User [Pagination]" . "===" . $th->getMessage());
        }
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validate
        try {
            $validator = Validator::make($request->all(), [
                'uid' => 'required',
                'amountUSD' => 'required',
                'amountBTC' => 'required',
                'wallet' => 'required',
                'status' => 'required',
            ]);


            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            //check if imcoing amountUSD is greater than the user's balance
            if ($request->amountUSD > auth()->user()->profile->available_balance) {
                return response()->json(['error' => 'Insufficient Balance'], 422);
            }


            $withdrawal = Auth::user()->withdrawals()->create($request->all());

            $withdrawal->user->profile->update([
                'withdrawal_point' => $withdrawal->user->profile->withdrawal_point - 1
            ]);

            $withdrawals = Withdrawal::where([[
                'user_id', auth()->id(),
            ]])->latest()->paginate(10);

            // Send MAIL - Withdrawal Initiated {Pending}
            dispatch(new UserWithdrawalInitiated(Auth::user(), $withdrawal));
            dispatch(new AdminWithdrawalInitiated(Auth::user(), $withdrawal));

            return response()->json(["message" => "Withdrawal Created Successfully", "withdrawals" => $withdrawals], 201);
        } catch (Exception $e) {
            Log::error("Creating Withdrawal" . "===" .  $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Withdrawal  $withdrawal
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            $mode = $request->query('mode');

            if ($mode == "user") {
                $withdrawal = Withdrawal::with(['user'])->where([['id', $id], ['user_id', auth()->id()]])->first();
                $withdrawal['userProfile'] = $withdrawal->user->profile;
                return response()->json(['withdrawal' => $withdrawal], 200);
            } else {
                $withdrawal = Withdrawal::with(['user'])->where([['id', $id]])->first();
                $withdrawal['userProfile'] = $withdrawal->user->profile;
                return response()->json(['withdrawal' => $withdrawal], 200);
            }
        } catch (\Throwable $th) {
            Log::error("Show Withdrawal" . "===" .  $th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Withdrawal  $withdrawal
     * @return \Illuminate\Http\Response
     */
    public function edit(Withdrawal $withdrawal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Withdrawal  $withdrawal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Withdrawal $withdrawal)
    {
        try {
            /* ----------- Validate Incoming Request ---------- */
            $validator = Validator::make($request->all(), [
                'status' => 'sometimes|required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $withdrawal->update([
                "status" => $request->status
            ]);

            $message = "";
            if ($request->status === 'pending') {
                $message = "Withdrawal Updated! Your Withdrawal is pending. Awaiting Admin.";
            }
            if ($request->status === 'rejected') {
                $message = "Withdrawal Updated! Your Withdrawal has been rejected. Contact Support.";
            }
            if ($request->status === 'processed') {
                //deduct amount from user's available_balance
                $withdrawal->user->profile->update([
                    'available_balance' => $withdrawal->user->profile->available_balance - $withdrawal->amountUSD
                ]);
                dispatch(new UserWithdrawalProcessed(Auth::user(), $withdrawal));
                $message = "Withdrawal Updated! Your Withdrawal has been processed.";
            }

            return response()->json(['message' => $message], 201);
        } catch (Exception $e) {
            Log::error("Updating Withdrawal" . "===" .  $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Withdrawal  $withdrawal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Withdrawal $withdrawal)
    {
        //
    }
}
