<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Conversation;
use App\Models\MessageAttachment;
use Illuminate\Http\Request;
use App\Events\SendMessageEvent;
use Illuminate\Support\Facades\DB;

function sendMsg (Request $request, $convo){
    $msg = Message::create($request->all());
    $msg->conversationId = $convo->id;
    $msg->save();
    $convo->lastMessageId = $msg->id;
    $convo->save();
    if (request()->hasFile('attachment')) {
        //! Using the Storage facade
        $path = 'https://' . $_SERVER['SERVER_NAME'] . '/storage/' . request()->attachment->store('attachments', 'public');
        MessageAttachment::create([
            'messageId'=> $msg->id,
            'path'=> $path,
        ]);
        
    }
    return $msg;
}
class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function sendMessage(Request $request)
    {
        $convo = Conversation::
            where(function ($query) {
                $query->where(["userId1" => request()->senderId, "userId2" => request()->receiverId]);
            })
            ->orWhere(function ($query) {
                $query->where(["userId1" => request()->receiverId, "userId2" => request()->senderId]);
            })
            ->first();
        if ($convo) {
            $msg =sendMsg($request, $convo);
            SendMessageEvent::dispatch($msg);
            $msg=$msg->with([
                'sender' => function ($query) {
                    $query->select('id', 'name', 'imageUrl');
                }
            ])
            ->with('attachments')
            ->find($msg->id);
            return response()->json([
                "message" => $msg,
                "status" => true,
            ]);
        } else {
            $convo = Conversation::create([
                "userId1" => $request->senderId,
                "userId2" => $request->receiverId,
            ]);
            $msg =sendMsg($request, $convo);
            $msg=$msg->with([
                'sender' => function ($query) {
                    $query->select('id', 'name', 'imageUrl');
                }
            ])
            ->with('attachments')
            ->find($msg->id);
            SendMessageEvent::dispatch($msg);
            return response()->json([
                "message" => $msg,
                "status" => true,
            ]);
        }

    }

    public function getChatList()
    {
        // select('userId1','userId2','lastMessageId',DB::raw('userId1-'.$user->id.' as userOne ,userId2-'.$user->id.' as userTwo'))->
        $user = auth()->user();
        $convos = Conversation::
            where("userId1", $user->id)
            ->orWhere("userId2", $user->id)
            ->with("lastMessage")
            ->with([
                'user1' => function ($query) {
                    $query->select('id', 'name', 'imageUrl')
                    ->with([
                        'favoritee' => function ($query) {
                            $query->select('userId')->where('userId', '=', auth()->user()->id);
                        }
                    ])
                    ->withCount('followers')
                    ->where('id', '!=', auth()->user()->id);
                }
            ])
            ->with([
                'user2' => function ($query) {
                    $query->select('id', 'name', 'imageUrl')
                    ->with([
                        'favoritee' => function ($query) {
                            $query->select('userId')->where('userId', '=', auth()->user()->id);
                        }
                    ])
                    ->withCount('followers')
                    ->where('id', '!=', auth()->user()->id);
                }
            ])
            // ->withCount("followers")
            ->orderBy("updated_at","desc")
            ->get();

        return response()->json([
            "chatList"=> $convos,
            "status"=> true,
        ]);
    }

    public function getConversation(){
        $conversation=Message::where("conversationId",request()->conversationId)
        ->with([
            'sender' => function ($query) {
                $query->select('id', 'name', 'imageUrl');
            }
        ])
        ->orderBy('created_at','desc')
        ->simplePaginate(20);

        return response()->json([
            "conversation"=> $conversation,
            "status"=> true,
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
