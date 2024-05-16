<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\FavoriteConversation;
use App\Models\TrashConversation;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    public function createArchive(Request $request)
    {
        try {
            $arc=Archive::where('userId', '=', $request->userId)->first();
            if ($arc) {
                $arc->delete();
                return response()->json([
                    "status" => true,
                    "msg" => "Archive Removed"
                ]);
            } else {
                $a = Archive::create($request->all());
                return response()->json([
                    "status" => true,
                    "msg" => "Added To Archive",
                    "data" => $a
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage(),
                'status' => false
            ]);
        }
    }
    public function getArchive(Archive $archive)
    {
        try {
            // $a = Archive::create($request->all());
            return response()->json([
                "status" => true,
                "data" => $archive
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage(),
                'status' => false
            ]);
        }
    }
    public function getUserArchives($userId)
    {
        try {
            $a = Archive::where('userId', '=', $userId)
                ->with([
                    'conversations' => function ($query) {
                        $user = auth()->user();
                        $query->where("userId1", $user->id)
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
                            ]);
                    }
                ])
                ->get();
            return response()->json([
                "status" => true,
                "data" => $a
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage(),
                'status' => false
            ]);
        }
    }
    public function createFavoriteConversation(Request $request)
    {
        try {
            $fav=FavoriteConversation::where('userId', '=', $request->userId)->first();
            if ($fav) {
                $fav->delete();
                return response()->json([
                    "status" => true,
                    "msg" => "Favorite Chat Removed"
                ]);
            } else {
                $a = FavoriteConversation::create($request->all());
                return response()->json([
                    "status" => true,
                    "msg" => "Added Favorite Chat",
                    "data" => $a
                ]);
            }
            
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage(),
                'status' => false
            ]);
        }
    }
    public function getFavoriteConversation(FavoriteConversation $favoriteConversation)
    {
        try {
            // $a = Archive::create($request->all());
            return response()->json([
                "status" => true,
                "data" => $favoriteConversation
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage(),
                'status' => false
            ]);
        }
    }
    public function getUserFavoriteConversation($userId)
    {
        try {
            $f = FavoriteConversation::where('userId', '=', $userId)
                ->with([
                    'conversations' => function ($query) {
                        $user = auth()->user();
                        $query->where("userId1", $user->id)
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
                            ]);
                    }
                ])
                ->get();
            return response()->json([
                "status" => true,
                "data" => $f
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage(),
                'status' => false
            ]);
        }
    }
    public function createTrashConversation(Request $request)
    {
        try {
            $trash=TrashConversation::where('userId', '=', $request->userId)->first();
            if ($trash) {
                $trash->delete();
                return response()->json([
                    "status" => true,
                    "msg" => "Trash Recycled"
                ]);
            } else {
                $a = TrashConversation::create($request->all());
                return response()->json([
                    "status" => true,
                    "msg" => "Trashed",
                    "data" => $a
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage(),
                'status' => false
            ]);
        }
    }
    public function getTrashConversation(TrashConversation $trashConversation)
    {
        try {
            // $a = Archive::create($request->all());
            return response()->json([
                "status" => true,
                "data" => $trashConversation
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage(),
                'status' => false
            ]);
        }
    }
    public function getUserTrashConversation($userId)
    {
        try {
            $f = TrashConversation::where('userId', '=', $userId)
                ->with([
                    'conversations' => function ($query) {
                        $user = auth()->user();
                        $query->where("userId1", $user->id)
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
                            ]);
                    }
                ])
                ->get();
            return response()->json([
                "status" => true,
                "data" => $f
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage(),
                'status' => false
            ]);
        }
    }
    

}
