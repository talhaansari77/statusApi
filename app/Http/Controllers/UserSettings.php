<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserSettings extends Controller
{

    public function changeUserEmail(Request $request)
    {
        try {
            $request->validate([
                'password' => 'string|min:6',
                'newEmail' => 'email|unique:users,email',
            ]);

            $user = auth()->user();
            $attempt = Hash::check($request->password, $user->password);

            if ($attempt) {
                $user->email = $request->newEmail;
                $user->save();
                return response()->json([
                    'user' => $user,
                    'msg' => 'Email updated',
                    'status' => true
                ]);
            } else {
                return response()->json([
                    'msg' => 'invalid Password',
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
    public function changeUserPassword(Request $request)
    {
        try {
            $request->validate([
                'currentPassword' => 'string|min:6',
                'newPassword' => 'string|min:6',
            ]);

            $user = auth()->user();
            $attempt = Hash::check($request->currentPassword, $user->password);

            if ($attempt) {
                $user->password = bcrypt($request->newPassword);
                $user->save();
                return response()->json([
                    'msg' => 'Password updated',
                    'status' => true
                ]);
            } else {
                return response()->json([
                    'msg' => 'invalid Password',
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
    public function updateSettings(Request $request)
    {
        try {
            $newSettings = $request->validate([
                'privacyLvl' => 'string',
                'blockedAccounts' => 'string',
                'pushNotification' => 'boolean',
                'instagramLink' => 'string',
                'youtubeLink' => 'string',
            ]);

            $oldSettings = auth()->user()->settings;
            $oldSettings->update($newSettings);
            // save new settings
            $oldSettings->save();

            return response()->json([
                'msg' => 'settings updated',
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

    public function deleteUser()
    {
        try {
            $con = Conversation::where('userId1',request()->id)
            ->orWhere('userId2',request()->id)->first();
            Message::where("conversationId", $con->id)->delete();
            $con->delete();

            $user = User::
                with('favorites')
                ->with('favoritee')
                ->with('settings')
                ->with('channel')
                ->with('comments')
                ->with('followers')
                ->with('following')
                ->with('blockers')
                ->with('blocked')
                ->with('posts')
                ->findOrFail(request()->id)
                ->delete();

            return response()->json([
                'msg' => 'User Deleted',
                'status' => true
            ]);



        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                // 'msg' => $th->getMessage(),
                'msg' => 'User Not Found',
                'status' => false
            ]);
        }
    }

    public function logout()
    {
        try {
            auth()->user()->tokens()->delete();
            // $request->user()->currentAccessToken()->delete();
            return response([
                'status' => true,
                'message' => 'Successfully Logged Out !!'
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
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
