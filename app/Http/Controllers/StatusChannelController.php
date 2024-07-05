<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\StatusChannel;

class StatusChannelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getChannel()
    {
        $channel = auth()->user()->channel()->with('posts')->get();
        return response()->json([
            "status" => true,
            'channel' => $channel,
        ]);
    }
    function sorting($a,$b) 
    { 
        if ($a->channel->id==$b->channel->id) return 0; 
            return ($a->channel->id<$b->channel->id)?-1:1; 
    } 
    public function getFollowingChannel()
    {
        $user = auth()->user();
        // $channel = $user->with('channel')->with('following')->find($user->id);
        $channel = User::select('id', 'name', 'imageUrl', 'location', 'link', 'bio')->with([
            'following' => function ($query) {
                $query->select('users.id', 'name', 'imageUrl', 'location', 'link', 'bio')->with([
                    'channel' => function ($query) {
                        $query->select('*')->withCount("posts")->with('lastPost')
                            ->with([
                                'posts' => function ($query) {
                                    $query->select('*')->with([
                                        'views' => function ($query) {
                                            $query->select('*')->where('user_id', auth()->user()->id);
                                        }
                                    ]);
                                }
                            ]);
                    }
                ])
                ->withCount([
                    'blockers' => function ($query) {
                        $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                    }
                ])
                ->withCount('following')
                ->havingNull('blockers_count');
            }
        ])
        ->find($user->id);
        

        // uksort($arr, "my_sort");
        // ->where('id', $user->id)
        // ->get();
        // $channel = User::find($user->id)->get(['id','name']);
        // $channel = auth()->user()->channel()->with('posts')->get();
        return response()->json([
            "sort" => uasort($channel->following,"sorting"),
            "status" => true,
            'channel' => $channel,
        ]);
    }
    public function getFavoritesChannel()
    {
        $user = auth()->user();
        // $channel = $user->with('channel')->with('following')->find($user->id);
        $channel = User::select('id', 'name', 'imageUrl', 'location', 'link', 'bio')->with([
            'favorites' => function ($query) {
                $query->select('users.id', 'name', 'imageUrl', 'location', 'link', 'bio')->with([
                    'channel' => function ($query) {
                        $query->select('*')->with('lastPost');
                    }
                ])
                ->withCount([
                    'blockers' => function ($query) {
                        $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                    }
                ])
                ->withCount('following')
                ->havingNull('blockers_count');
            }
        ])
            ->find($user->id);
        // ->where('id', $user->id)
        // ->get();
        // $channel = User::find($user->id)->get(['id','name']);
        // $channel = auth()->user()->channel()->with('posts')->get();
        return response()->json([
            "status" => true,
            'channel' => $channel,
        ]);
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
