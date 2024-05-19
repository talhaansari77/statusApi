<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use App\Models\View;
use Illuminate\Http\Request;
use App\Models\StatusChannel;
use App\Events\ChannelUpdatesEvent;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function createPost(Request $request)
    {
        try {
            $post = Post::create($request->all());
            if (request()->hasFile('imageUrl')) {
                //! Using the Storage facade
                $post->imageUrl = 'https://' . $_SERVER['SERVER_NAME'] . '/storage/' . request()->imageUrl->store('postImages', 'public');
                $post->save();
            }
            $post->title = auth()->user()->name;
            $post->save();
            $channel = StatusChannel::find($post->channelId);
            $channel->lastPostId = $post->id;
            $channel->save();
            ChannelUpdatesEvent::dispatch($post);
            return response()->json([
                'post' => Post::find($post->id),
                'status' => true
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'msg' => $th->getMessage(),
                'status' => false
            ]);
        }
    }

    public function addRemoveViews(Request $request)
    {
        try {
            $liked = View::
                where(['user_id' => $request->user_id, 'post_id' => $request->post_id])
                ->first();
            if ($liked) {
                // $liked->delete();
                return response()->json([
                    'msg' => 'viewed already',
                    'status' => true
                ]);
            } else {
                $liked = View::create($request->all());
                return response()->json([
                    'msg' => 'view added',
                    'status' => true
                ]);
            }

        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage(),
                'status' => false
            ]);
        }
    }
    public function addRemoveLikes(Request $request)
    {
        try {
            $liked = Like::
                where(['user_id' => $request->user_id, 'post_id' => $request->post_id])
                ->first();
            if ($liked) {
                $liked->delete();
                return response()->json([
                    'msg' => 'unLiked',
                    'status' => true
                ]);
            } else {
                $liked = Like::create($request->all());
                return response()->json([
                    'msg' => 'liked',
                    'status' => true
                ]);
            }

        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage(),
                'status' => false
            ]);
        }
    }

    public function updatePost(Request $request)
    {
        try {
            // dd($request->imageUrl);
            $post = Post::findOrFail($request->id);
            // if (isset($request->title)) {
            //     $post->title = $request->title;
            // }
            if (isset($request->description)) {
                $post->description = $request->description;
            }
            if ($request->hasFile('imageUrl')) {
                //! Using the Storage facade
                $post->imageUrl = 'https://' . $_SERVER['SERVER_NAME'] . '/storage/' . request()->imageUrl->store('postImages', 'public');
            }
            // if (isset($request->views)) {
            //     $post->views += 1;
            // }
            // if (isset($request->likes)) {
            //     $post->likes += 1;
            // }
            $post->save();
            ChannelUpdatesEvent::dispatch($post);
            return response()->json([
                'msg' => 'post updated',
                'post' => $post,
                'status' => true
            ]);

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
    public function deletePost(Post $post)
    {
        try {
            $post->delete();
            // ChannelUpdatesEvent::dispatch($post);
            return response()->json([
                'msg' => 'post deleted',
                'status' => true
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'msg' => $th->getMessage(),
                'status' => false
            ]);
        }

    }
    public function getStatus(User $user)
    {
        // $posts = Post::where('channelId', $user->id)->get();
        return response()->json([
            'posts' => $user->channel
                ->posts()
                ->withCount('likes')
                ->withCount('views')
                // ->orderBy('id', 'desc')
                ->simplePaginate(50),
            'status' => true
        ]);
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
