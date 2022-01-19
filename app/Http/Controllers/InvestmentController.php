<?php

namespace App\Http\Controllers;

use App\Jobs\Admin\AdminInvestmentInitiated;
use App\Jobs\Admin\AdminInvestmentInprogress;
use App\Jobs\User\UserInvestmentInitiated;
use App\Jobs\User\UserInvestmentInprogress;
use App\Models\Investment;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InvestmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $investments = Investment::latest()->paginate(10);

            return response()->json(['investments' => $investments]);
        } catch (\Throwable $th) {
            Log::error("List all Investments for Admin [Pagination]" . "===" . $th->getMessage());
        }
    }

    /**
     * Paginate the given request for a given model.
     *
     * @return \Illuminate\Http\Response
     */
    public function paginate()
    {
        try {
            $investments = Investment::where([[
                'user_id', auth()->id(),
            ]])->latest()->paginate(10);

            return response()->json(['investments' => $investments]);
        } catch (\Throwable $th) {
            Log::error("List Investments [Pagination]" . "===" . $th->getMessage());
        }
    }


    /**
     * Return all Ongoing Investments
     *
     * @return \Illuminate\Http\Response
     */
    public function ongoing()
    {
        try {
            $investments = Investment::with(['user'])->where([
                ['user_id', auth()->id()],
                ['status', 'inprogress']
            ])->latest()->paginate(10);

            return response()->json(['investments' => $investments]);
        } catch (\Throwable $th) {
            Log::error("List Ongoing Investments [Pagination]" . "===" . $th->getMessage());
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
                'amountUSD' => 'required',
                'amountBTC' => 'required',
                'plan' => 'required',
                'reference' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $hours = env("INV_ELASPE_INTERVAL", 1);
            $data = [
                'amountUSD' => $request->amountUSD,
                'amountBTC' => $request->amountBTC,
                'plan' => $request->plan,
                'reference' => $request->reference,
                'status' => 'created',
                'elapse_at' => date("Y-m-d H:i:s", strtotime("+{$hours} hours")),
            ];

            $investment = Auth::user()->investments()->create($data);

            return response()->json(["investment" => $investment], 201);
        } catch (Exception $e) {
            Log::error("Creating Investment" . "===" .  $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Investment  $investment
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            $mode = $request->query('mode');

            if ($mode == "user") {
                $investment = Investment::with(['user'])->where([['id', $id], ['user_id', auth()->id()]])->first();
                $investment['userProfile'] = $investment->user->profile;
                return response()->json(['investment' => $investment], 200);
            } else {
                $investment = Investment::with(['user'])->where([['id', $id]])->first();
                $investment['userProfile'] = $investment->user->profile;
                return response()->json(['investment' => $investment], 200);
            }
        } catch (\Throwable $th) {
            Log::error("Show Investment" . "===" .  $th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Investment  $investment
     * @return \Illuminate\Http\Response
     */
    public function edit(Investment $investment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Investment  $investment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Investment $investment)
    {
        try {
            /* ----------- Validate Incoming Request ---------- */
            $validator = Validator::make($request->all(), [
                'credit_reference' => 'sometimes|required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $data = [
                "credit_reference" => $request->credit_reference,
                "status" => $request->status,
            ];

            $investment->update($data);

            if ($request->status == 'inprogress') {
                $days = 0;
                $point = 0;
                if ($investment->plan == 'hydrogen' || $investment->plan == 'helium') {
                    $days = 84;
                    $point = 2;
                } else {
                    $days = 35;
                    $point = 4;
                }
                //update user's withdrawal point
                $user = User::find($investment->user_id);
                $user->profile->withdrawal_point += $point;
                $user->profile->save();

                //update investment_end_date set to investment_updated_at + $days
                $investment->update([
                    'investment_end_date' => date("Y-m-d H:i:s", strtotime("+{$days} days")),
                ]);
            }

            $message = "";

            if ($request->status === 'initiated') {
                dispatch(new UserInvestmentInitiated(Auth::user(), $investment));
                dispatch(new AdminInvestmentInitiated(Auth::user(), $investment));
                $message = "Investment Updated! Your Investment has been Initiated. Awaiting Admin.";
            }
            if ($request->status === 'inprogress') {
                dispatch(new UserInvestmentInprogress(Auth::user(), $investment));
                dispatch(new AdminInvestmentInprogress(Auth::user(), $investment));
                $message = "Investment Updated! Your Investment is Ongoing.";
            }
            if ($request->status === 'completed') {
                dispatch(new UserInvestmentInprogress(Auth::user(), $investment));
                dispatch(new AdminInvestmentInprogress(Auth::user(), $investment));
                $message = "Investment Updated! Your Investment is Ongoing.";
            }

            return response()->json(['message' => $message], 201);
        } catch (Exception $e) {
            Log::error("Updating Investment" . "===" .  $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Investment  $investment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Investment $investment)
    {
        //
    }




    /**
     * Dash Card Data
     *
     * To Fetch Data Dashboard Cards
     */
    public function dashCardData()
    {
        try {
            //get the amountUSD of the last investment of the auth user with status not initiated or abandoned
            $amountUSD = Investment::where([
                ['user_id', auth()->id()],
                ['status', '!=', 'created'],
                ['status', '!=', 'abandoned'],
            ])->latest()->first()->amountUSD;
            //get the sum of amountUSD of all investments of the auth user with status not initiated or abandoned
            $totalInvestmentUSD = Investment::where([
                ['user_id', auth()->id()],
                ['status', '!=', 'created'],
                ['status', '!=', 'abandoned'],
            ])->sum('amountUSD');
            //get the count of all investments of the auth user with status not initiated or abandoned
            $totalInvestmentCount = Investment::where([
                ['user_id', auth()->id()],
                ['status', '!=', 'created'],
                ['status', '!=', 'abandoned'],
            ])->count();

            $dashCardData = [
                'amountUSD' => $amountUSD,
                'totalInvestmentUSD' => $totalInvestmentUSD,
                'totalInvestmentCount' => $totalInvestmentCount,
            ];

            return response()->json(["dashCardData" => $dashCardData], 200);
        } catch (\Throwable $th) {
            Log::error("Getting Dash Card Data" . "===" .  $th->getMessage());
        }
    }



    /**
     * Chart Data
     *
     * To Fetch Data for Chart API
     */
    public function chartData()
    {
        try {
            /* ------------------------- Line  Chart Record ------------------------- */

            //Get Current Year
            $now = Carbon::now();
            $current_Year = $now->format('Y');

            //Fetch User Investments for $current_Year orderby created_at
            $investments = Investment::where([
                ['user_id', auth()->id()],
            ])->whereYear('created_at', $current_Year)->orderBy('created_at', 'asc')->get();

            //Get Months from Transactions
            $months = [];
            foreach ($investments as $investment) {
                array_push($months, $investment->created_at->format('m'));
            }
            $filteredMonths = array_unique($months);

            //Initiate Line Chart Data
            $lineChart = array(
                'months' => [],
                'amounts' => []
            );

            //Get the sum of amount sent for a $month
            foreach ($filteredMonths as $month) {
                $monthInvestment = Investment::where([
                    ['user_id', auth()->id()],
                    ['status', '!=', 'created'],
                    ['status', '!=', 'abandoned'],
                ])->whereMonth('created_at', $month)->get();

                $summedFinalAmt = $monthInvestment->sum('amountUSD');

                //Push the  summed Amount for the month
                array_push(
                    $lineChart['amounts'],
                    $summedFinalAmt
                );

                //Push the  Month
                array_push(
                    $lineChart['months'],
                    $monthInvestment[0]->created_at->format('M')
                );
            }

            /* ------------------------- Fetch Pie Chart Record ------------------------- */
            $statuses = [];
            //Get All Statuses
            foreach ($investments as $investment) {
                array_push($statuses, $investment->status);
            }
            $filteredStatuses = array_unique($statuses);

            //Initiate Pie Chart Data
            $pieChart = array(
                'status' => [],
                'number' => []
            );

            //Get the count of investment for a status
            foreach ($filteredStatuses as $status) {
                $statusInvestment = Investment::where([[
                    'user_id', auth()->id(),
                ], ['status', $status]])->orderBy('status')->get();

                $countStatus = $statusInvestment->count('status');

                //Push the  count of status
                array_push(
                    $pieChart['number'],
                    $countStatus
                );

                //Push the  staus
                array_push(
                    $pieChart['status'],
                    $status
                );
            }

            $chartData = array(
                "lineChart" => $lineChart,
                "pieChart" => $pieChart,
            );

            return response()->json(['chartData' => $chartData]);
        } catch (\Throwable $th) {
            Log::error("Getting Chart Data" . "===" .  $th->getMessage());
        }
    }
}
