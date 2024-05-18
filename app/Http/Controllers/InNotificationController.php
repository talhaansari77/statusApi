<?php

namespace App\Http\Controllers;

use App\Models\InNotification;
use Illuminate\Http\Request;

class InNotificationController extends Controller
{
    //
    public function getInNotifications(){
        try {

            $n=InNotification::where("receiverId", auth()->user()->id)->get();

            return response()->json([
                "InNotification"=> $n,
                "status"=>true,
                ]);
        } catch (\Throwable $th) {
           return response()->json([
            "msg"=> $th->getMessage(),
            "status"=>false,
            ]);
        }
    }
}
