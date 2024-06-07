<?php

namespace App\Http\Controllers;

use App\Events\InNotificationEvent;
use App\Jobs\CommentNotificationJob;
use App\Models\InNotification;
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
        // included_segments=>["Active Users", "Inactive Users"]
        // function sendMessage(){
        $content = array(
            "en" => 'hello'
        );

        $fields = array(
            'app_id' => "32945f51-424b-4932-a5cc-f5dc0b54937c",
            'included_segments' => array('All'),
            'data' => array("foo" => "bar"),
            'large_icon' => "ic_launcher_round.png",
            'contents' => $content
        );

        $fields = json_encode($fields);
        print ("\nJSON sent:\n");
        print ($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic MjAwMjhlN2ItNGJiYi00ODg2LWE5ZTgtY2NiNzQzNTk2MGIz'
        )
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        // return $response;
        // }
        // TestEvent::dispatch($request->msg);
        return response()->json([
            'data' => $response,
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
            CommentNotificationJob::dispatch($c);
            $n=InNotification::create([
                "imageUrl"=>$user->imageUrl,
                "username"=>$user->name,
                "description"=>"Wrote on your wall",
                "forComment"=>true,
                "senderId"=>$user->id,
                "receiverId"=>$request->userId,
            ]);
            InNotificationEvent::dispatch($n);

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
            // $comments = User::find($request->userId)->comments();
            $comments = User::where(['id'=>$request->userId,'wallComments'=>1])
            ->first()
            ->comments()
            ->with(['commentator'=>function($query){
                $query->select('id','name','imageUrl');
            }]);

            return response()->json([
                'comments' => $comments->simplePaginate(10),
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
