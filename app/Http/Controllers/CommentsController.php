<?php

namespace App\Http\Controllers;

use stdClass;
use App\Models\User;
use App\Models\Comment;
use App\Events\TestEvent;
use App\Events\CommentEvent;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    public function sendChat(Request $request)
    {
        TestEvent::dispatch($request->msg);
        return response()->json([
            'msg' => 'msg sent',
            'status' => true
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createComment(Request $request)
    {
        try {
            $user = auth()->user();
            $r = $request->validate([
                'description' => 'required|string',
                'userId' => 'required',
            ]);
            $r['imageUrl'] = $user->imageUrl;
            $r['commentatorId'] = $user->id;
            $r['username'] = $user->name;

            $c = Comment::create($r);
            CommentEvent::dispatch($c);

            return response()->json([
                'comment' => $c,
                'msg' => 'comment added',
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
    public function getUserComment(Request $request)
    {
        try {
            $comments = User::find($request->userId)->comments();

            return response()->json([
                'comments' => $comments->orderBy('id', 'desc')->simplePaginate(10),
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
    public function deleteComment(Request $request)
    {
        try {
            $request->validate([
                'commentId' => 'required',
            ]);
            $r = Comment::find($request->commentId);
            $userId = $r->userId;
            $r = $r->delete();
            // del response
            $delCom = new stdClass();
            $delCom->commentId = $request->commentId;
            $delCom->userId = $request->userId;
            $delCom->deleted = true;
            CommentEvent::dispatch($delCom);
            if ($r) {
                return response()->json([
                    'msg' => 'Comment Deleted',
                    'comment' => $delCom,
                    'status' => true
                ]);
            } else {
                return response()->json([
                    'msg' => 'Comment Could Not be Deleted',
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
