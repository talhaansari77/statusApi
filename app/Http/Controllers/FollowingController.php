<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Following;
use Illuminate\Http\Request;

class FollowingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function follow(Request $request)
    {
        try {
            $r = $request->validate([
                'followee' => 'integer|required',
                // 'follower'=>'integer|required'
            ]);

            $followee = User::find($r['followee']);
            $follower = User::find(auth()->user()->id);

            $following = Following::where('followee', $followee->id)
                ->where('follower', $follower->id)->first();
            // dd($following);

            if ($followee && $follower) {
                if ($following) {
                    $following->delete();
                    return response()->json([
                        'msg' => 'unfollowed',
                        'status' => true
                    ]);
                } else {
                    $r['follower'] = $follower->id;
                    Following::create($r);
                    return response()->json([
                        'msg' => 'followed',
                        'status' => true
                    ]);
                }
            } else {
                return response()->json([
                    'msg' => 'user not found',
                    'status' => false
                ]);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'msg' => $th->getMessage(),
                'status' => false
            ]);
        }
    }
    public function isFollowing(Request $request)
    {
        try {
            $r = $request->validate([
                'followee' => 'integer|required',
                // 'follower'=>'integer|required'
            ]);

            $followee = User::find($r['followee']);
            $follower = User::find(auth()->user()->id);

            $following = Following::where('followee', $followee->id)
                ->where('follower', $follower->id)->first();
            // dd($following);

            if ($followee && $follower) {
                if ($following) {
                    return response()->json([
                        'isFollowing' => true,
                        'status' => true
                    ]);
                } else {
                    return response()->json([
                        'isFollowing' => false,
                        'status' => true
                    ]);
                }
            } else {
                return response()->json([
                    'msg' => 'user not found',
                    'status' => false
                ]);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'msg' => $th->getMessage(),
                'status' => false
            ]);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function getUsers()
    {
        try {
            $user = auth()->user();
            request()->validate([
                'get' => 'string|required'
            ]);
            if (request()->get == 'following') {
                // ->simplePaginate(15)
                $following = $user->following()->withCount('following')->withCount('followers')->with('comments')->simplePaginate();
                if ($following->count()) {

                    return response()->json([
                        'users' => $following,
                        'status' => true
                    ]);
                } else {
                    return response()->json([
                        'msg' => 'no results',
                        'status' => true
                    ]);
                }
            } else {
                // ->simplePaginate(15)
                $allUsers = User::withCount('following')->withCount('followers')->with('comments')->simplePaginate();

                if ($allUsers->count()) {

                    return response()->json([
                        'users' => $allUsers,
                        'status' => true
                    ]);
                } else {
                    return response()->json([
                        'msg' => 'no results',
                        'status' => true
                    ]);
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'msg' => $th->getMessage(),
                'status' => false
            ]);
        }
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
