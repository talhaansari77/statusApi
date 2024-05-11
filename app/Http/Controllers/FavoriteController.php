<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function favorite(Request $request)
    {
        try {
            $r = $request->validate([
                'favorite' => 'required|string',
                // 'follower'=>'integer|required'
            ]);

            $favorite = User::find($r['favorite']);
            $user = User::find(auth()->user()->id);

            $favorites = Favorite::where('favorite', $favorite->id)
                ->where('userId', $user->id)->first();
            // dd($following);

            if ($favorite && $user) {

                if ($favorites) {
                    $favorites->delete();
                    return response()->json([
                        'msg' => 'favorite removed',
                        'status' => true
                    ]);
                } else {
                    $r['userId'] = $user->id;
                    // dd($r);
                    Favorite::create($r);
                    return response()->json([
                        'msg' => 'favorite added',
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
    public function isFavorite(Request $request)
    {
        try {
            $r = $request->validate([
                'favorite' => 'required|string',
                // 'follower'=>'integer|required'
            ]);

            $favorite = User::find($r['favorite']);
            $user = User::find(auth()->user()->id);

            $favorites = Favorite::where('favorite', $favorite->id)
                ->where('userId', $user->id)->first();
            // dd($following);

            if ($favorite && $user) {

                if ($favorites) {
                    return response()->json([
                        'isFavorite' => true,
                        'status' => true
                    ]);
                } else {
                    return response()->json([
                        'isFavorite' => false,
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

    public function getFavoriteUsers()
    {
        try {
            $user = auth()->user();
            // ->simplePaginate(15)
            $favorites = auth()->user()->favorites()->withCount('following')->withCount('followers')->with('comments')->simplePaginate();
            if ($favorites->count()) {

                return response()->json([
                    'users' => $favorites,
                    'status' => true
                ]);
            } else {
                return response()->json([
                    'msg' => 'no results',
                    'status' => true
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
