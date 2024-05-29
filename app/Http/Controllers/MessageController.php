<?php

namespace App\Http\Controllers;

use App\Events\TypingEvent;
use App\Jobs\NotificationJob;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\MessageAttachment;
use Illuminate\Http\Request;
use App\Events\SendMessageEvent;
use Illuminate\Support\Facades\DB;

function sendMsg(Request $request, $convo)
{
    $msg = Message::create($request->all());
    $msg->conversationId = $convo->id;
    $msg->save();
    $convo->lastMessageId = $msg->id;
    $convo->save();
    if (request()->hasFile('attachment')) {
        //! Using the Storage facade
        $path = 'https://' . $_SERVER['SERVER_NAME'] . '/storage/' . request()->attachment->store('attachments', 'public');
        MessageAttachment::create([
            'messageId' => $msg->id,
            'path' => $path,
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
            $msg = sendMsg($request, $convo);
            $msg = $msg->with([
                'sender' => function ($query) {
                    $query->select('id', 'name', 'imageUrl');
                }
            ])
                ->with('attachments')
                ->find($msg->id);
            SendMessageEvent::dispatch($msg);
            NotificationJob::dispatch($msg);
            return response()->json([
                "message" => $msg,
                "status" => true,
            ]);
        } else {
            $convo = Conversation::create([
                "userId1" => $request->senderId,
                "userId2" => $request->receiverId,
            ]);
            $msg = sendMsg($request, $convo);
            $msg = $msg->with([
                'sender' => function ($query) {
                    $query->select('id', 'name', 'imageUrl');
                }
            ])
                ->with('attachments')
                ->find($msg->id);

            SendMessageEvent::dispatch($msg);
            NotificationJob::dispatch($msg);
            return response()->json([
                "message" => $msg,
                "status" => true,
            ]);
        }

    }
    public function readMessage(Message $message)
    {
        try {
            $message->read_at = now();
            $message->save();
            return response()->json([
                "message" => 'success',
                "status" => true,
            ]);
        } catch (\Throwable $th) {
            $message->read_at = now();
            $message->save();
            return response()->json([
                "message" => $th->getMessage(),
                "status" => false,
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
                "archiveCon" => function ($query) {
                    $query->select('*')->where('userId', '=', auth()->user()->id);
                }
            ])
            ->with([
                "favoriteCon" => function ($query) {
                    $query->select('*')->where('userId', '=', auth()->user()->id);
                }
            ])
            ->with([
                "trashCon" => function ($query) {
                    $query->select('*')->where('userId', '=', auth()->user()->id);
                }
            ])
            ->with([
                "blockedCon" => function ($query) {
                    $query->select('*')->where('userId', '=', auth()->user()->id);
                }
            ])
            ->with([
                'user1' => function ($query) {
                    $query->select('id', 'name', 'imageUrl','isOnline')
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
                    $query->select('id', 'name', 'imageUrl','isOnline')
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
            ->orderBy("updated_at", "desc")
            ->get();



        return response()->json([
            "chatList" => $convos,
            "status" => true,
        ]);
    }

    public function getConversation()
    {
        $conversation = Message::where("conversationId", request()->conversationId)
            ->with([
                'sender' => function ($query) {
                    $query->select('id', 'name', 'imageUrl');
                }
            ])
            ->with('attachments')
            // ->orderBy('created_at','desc')
            ->simplePaginate(50);

        return response()->json([
            "conversation" => $conversation,
            "status" => true,
        ]);
    }
    public function getConversationIfExist()
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
            $convo = $convo
                ->with("lastMessage")
                ->with([
                    "archiveCon" => function ($query) {
                        $query->select('*')->where('userId', '=', auth()->user()->id);
                    }
                ])
                ->with([
                    "favoriteCon" => function ($query) {
                        $query->select('*')->where('userId', '=', auth()->user()->id);
                    }
                ])
                ->with([
                    "trashCon" => function ($query) {
                        $query->select('*')->where('userId', '=', auth()->user()->id);
                    }
                ])
                ->with([
                    "blockedCon" => function ($query) {
                        $query->select('*')->where('userId', '=', auth()->user()->id);
                    }
                ])
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
                ])->where(function ($query) {
                    $query->where(["userId1" => request()->senderId, "userId2" => request()->receiverId]);
                })
                ->orWhere(function ($query) {
                    $query->where(["userId1" => request()->receiverId, "userId2" => request()->senderId]);
                })->first();

            return response()->json([
                "conversation" => $convo,
                "exist" => true,
                "status" => true,
            ]);
        }
        // ->with('attachments')
        // ->orderBy('created_at','desc')
        // ->simplePaginate(50);

        return response()->json([
            // "conversation" => $convo,
            "exist" => false,
            "status" => true,
        ]);
    }

    public function searchMessages($userId, $search)
    {
        $search = "%" . $search . "%";
        $result = Message::where("senderId", $userId)
            ->orWhere("receiverId", $userId)
            ->where('message', 'like', $search)->get();

        if ($result->count()) {
            return response()->json([
                'searchResult' => $result,
                'status' => true,
            ]);
        } else {
            return response()->json([
                'searchResult' => "not found",
                'status' => true,
            ]);
        }
    }

    public function deleteConversation(Conversation $conversation)
    {
        Message::
            with("attachments")
            ->where('conversationId', $conversation->id)
            ->delete();
        // Conversation::with()
        return response()->json([
            'searchResult' => $conversation->delete(),
            'status' => true,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */

     public function startTypingChannel(Request $request){
        $id1=intval($request->user1Id);
        $id2=intval($request->user2Id);
        TypingEvent::dispatch(['user1Id'=> $id1,'user2Id'=> $id2]);
        return response()->json([
            'status'=> true,
            'channel'=> 'TypingChannel'.$id1 +$id2,
            ]);

     }
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
