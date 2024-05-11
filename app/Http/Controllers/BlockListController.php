<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BlockList;
use Illuminate\Http\Request;

class BlockListController extends Controller
{

    public function blockUser(Request $request)
    {
        try {
            $r = $request->validate([
                'blocked' => 'string|required',
                // 'follower'=>'integer|required'
            ]);

            $blocked = User::find($r['blocked']);
            $blocker = User::find(auth()->user()->id);

            $blockList = BlockList::where('blocked', $blocked->id)
                ->where('blocker', $blocker->id)->first();
            // dd($following);

            if ($blocked && $blocker) {

                if ($blockList) {
                    $blockList->delete();
                    return response()->json([
                        'msg' => 'unblocked',
                        'status' => true
                    ]);
                } else {
                    $r['blocker'] = $blocker->id;
                    // dd($r);
                    BlockList::create($r);
                    return response()->json([
                        'msg' => 'blocked',
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
    public function getBlockedUsers()
    {
        try {
            $user = auth()->user();
            // ->simplePaginate(15)
            $blockList = auth()->user()->blocked()->simplePaginate();
            if ($blockList->count()) {

                return response()->json([
                    'users' => $blockList,
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
