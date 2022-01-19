<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLoggedInUser()
    {
        $user = User::with(['profile', 'role'])->find(Auth::id());

        $userscore = 20;

        if ($user->profile->first_name != null) {
            $userscore = $userscore + 10;
        }
        if ($user->profile->last_name != null) {
            $userscore = $userscore + 10;
        }
        if ($user->profile->address != null) {
            $userscore = $userscore + 10;
        }
        if ($user->profile->city != null) {
            $userscore = $userscore + 10;
        }
        if ($user->profile->state != null) {
            $userscore = $userscore + 10;
        }
        if ($user->profile->country != null) {
            $userscore = $userscore + 10;
        }
        if ($user->profile->zip != null) {
            $userscore = $userscore + 10;
        }
        if ($user->profile->phone != null) {
            $userscore = $userscore + 10;
        }


        $user['score'] = $userscore;

        return response()->json(['user' => $user], 200);
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            /* ----------- Validate Incoming Request ---------- */
            $validator = Validator::make($request->all(), [
                'first_name' => ['min:3'],
                'last_name' => ['min:3'],
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            //update auth user profile with $request->all();
            $user = User::find(Auth::id());
            $user->profile->update($request->all());

            return response()->json(['message' => 'Profile Updated Successfully'], 200);
        } catch (\Throwable $th) {
            Log::error("User Profile Update" . "===" . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
