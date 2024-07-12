<?php

namespace App\Http\Controllers;

use App\Models\Following;
use App\Models\User;
use App\Jobs\MailJob;
use App\Mail\WelcomeMail;
use Illuminate\Support\Str;
use App\Jobs\ForgetEmailJob;
use App\Models\UserSettings;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Models\StatusChannel;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

function generateOTP($secret, $time, $length = 6)
{
    // The OTP is a 6-digit code, so we use 6 zeros as the pad
    $paddedTime = str_pad($time, 16, '0', STR_PAD_LEFT);
    // Use the SHA1 algorithm to generate the OTP
    $hash = hash_hmac('sha1', $paddedTime, $secret, true);
    // Extract the 4 least significant bits of the last byte of the hash
    $offset = ord(substr($hash, -1)) & 0xF;
    // Extract a 4-byte string from the hash starting at the $offset
    $truncatedHash = substr($hash, $offset, 4);
    // Convert the 4-byte string to an integer
    $code = unpack('N', $truncatedHash)[1] & 0x7FFFFFFF;
    // Return the OTP as a 6-digit code
    return str_pad($code % 1000000, $length, '0', STR_PAD_LEFT);
}
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getAuth()
    {
        try {
            return response()->json([
                'user' => auth()
                    ->user()
                    ->with('channel')
                    ->with('settings')
                    ->withCount('following')
                    ->withCount('followers')
                    ->with('comments')
                    ->find(auth()->user()->id),
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
    public function getUserDetail()
    {
        try {
            $user = User::withCount('following')
                ->withCount('followers')
                ->with('comments')
                ->with('channel')
                ->with('settings')
                ->with([
                    'followers' => function ($query) {
                        $query->select('follower')->where('follower', '=', auth()->user()->id);
                    }
                ])
                ->with([
                    'favoritee' => function ($query) {
                        $query->select('userId')->where('userId', '=', auth()->user()->id);
                    }
                ])
                ->find(request()->id);

            return response()->json([
                'user' => $user,
                // 'channel' => auth()->user()->channel,
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



    public function register()
    {
        try {
            $u = User::where('email', request()->email)
                ->where('email_verified_at', null)->get();
            if (!$u->count()) {
                request()->validate([
                    'email' => 'required|email|unique:users',
                    'password' => 'required|min:6',
                    'deviceId' => [Rule::excludeIf(!strlen(request()->deviceId)),'string'],
                    'name' => [Rule::excludeIf(!strlen(request()->name)),'string'],
                    'birthday' => [Rule::excludeIf(!strlen(request()->birthday)),'string'],
                    'interestTags' => [Rule::excludeIf(!strlen(request()->interestTags)),'string'],
                ],[
                    'email.unique' => 'Email in use'
                ]);


                // $otp = rand(10, 100.. '2024');
                $otp = generateOTP(request('email'), now());
                $user = User::create([
                    'imageUrl' => 'https://giantcorp.us/storage/placeholder.png',
                    'email' => request('email'),
                    'password' => bcrypt(request('password')),
                    'deviceId' => request()->deviceId,
                    'name' => request()->name,
                    'birthday' => request()->birthday,
                    'interestTags' => request()->interestTags,
                    'otp' => $otp
                ]);
                // dd(['userId'=>$user->id]);
                $userSettings = UserSettings::create(['userId'=>$user->id]);
                $userChannel = StatusChannel::create(['userId'=>$user->id]);
                $data['user'] = $user;
                $data['mailTemplate'] = 'emails.welcome';
                MailJob::dispatch($data);
                // Mail::send('emails.welcome',['data'=>$user],function($msg) use ($user){
                //     $msg->to($user['email'])->subject('Your OTP For Status');
                // });



                // Mail::to($user['email'])->send(new WelcomeMail($user['email'], $otp));

                return response()->json([
                    'msg' => "A Code has been Sent Your Email",
                    'status' => true
                ]);
            } else {
                $otp = generateOTP(request('email'), now());
                $u = $u->first();
                $u->otp = $otp;
                $u->save();
                //code...
                $data['user'] = $u;
                $data['mailTemplate'] = 'emails.welcome';
                MailJob::dispatch($data);
                // Mail::send('emails.welcome',['data'=>['otp'=>$otp]],function($msg) {
                //     $msg->to(request('email'))->subject('Your OTP For Status');
                // });

                return response()->json([
                    'msg' => "account already exist. a new otp has been sent to your email to verify",
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

        // $token = $user->createToken($user->name)->plainTextToken;
        // sending verification link
        // event(new Registered($user));



    }

    public function resendOtp(Request $request)
    {
        try {
            $otp = generateOTP(request('email'), now());
            $user = $request->validate([
                'email' => 'required|email',
            ]);
            $user['otp'] = $otp;
            $u = User::where('email', $user['email'])
                ->where('email_verified_at', null)
                ->first();
            if ($u) {
                $u->otp = $otp;
                $u->save();
                // dd($u->otp);
                $data['user'] = $u;
                $data['mailTemplate'] = 'emails.welcome';
                MailJob::dispatch($data);
                //code...
                // Mail::send('emails.welcome',['data'=>$user],function($msg) use ($user){
                //     $msg->to($user['email'])->subject('Your OTP For Status');
                // });

                return response()->json([
                    'msg' => 'new code has been sent, check your your email',
                    'status' => true
                ]);
            } else {
                return response()->json([
                    'msg' => 'user does not exist or already verified',
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

    public function authenticate()
    {
        try {
            // dd(request()->all());
            $credentials = request()->validate([
                'email' => 'required|email',
                'password' => 'required|min:6'
            ]);

            $user = auth()->attempt($credentials);
            if ($user) {
                $user = User::where('email', request()->email)->first();
                if ($user->email_verified_at) {
                    $token = $user->createToken($user->email)->plainTextToken;
                    $user->deviceId=request()->deviceId;
                    $user->save();
                    // dd($token);

                    return response()->json([
                        'token' => $token,
                        'user' => auth()
                            ->user()
                            ->with('settings')
                            ->withCount('following')
                            ->withCount('followers')
                            ->with('comments')
                            ->with('channel')
                            // ->with('favorites')
                            ->find(auth()->user()->id),
                        'msg' => "Login Successful",
                        'status' => true
                    ]);
                } else {
                    return response()->json([
                        'msg' => "Unverified Email",
                        'status' => false
                    ]);
                }
            } else {
                return response()->json(['error' => 'invalid email or password', 'status' => false], 401);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'msg' => $th->getMessage(),
                'status' => false
            ]);
        }
    }
    public function verifyOtp()
    {
        try {
            $request = request()->validate([
                'otp' => 'required|digits:6',
            ]);

            $user = User::where(['otp' => $request['otp']])->first();


            if (!$user) {
                return response()->json([
                    'msg' => "invalid otp",
                    'status' => false
                ]);
            } else {
                $token = $user->createToken($user->email)->plainTextToken;
                $user->email_verified_at = now();
                $user->isActive = 1;
                $user->otp = 0;
                $user->save();

                $owner=User::where('email', 'shamrockfilms@gmail.com')->first();
                if($owner){
                    $following=Following::where(['followee'=> $owner->id,'follower'=> $user->id])->first();
                    if(!$following){
                        Following::create([
                            'followee'=> $owner->id,
                            'follower'=> $user->id,
                        ]);
                    }
                }
                return response()->json([
                    'msg' => 'you are verified',
                    'token' => $token,
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

    public function profileSetup()
    {
        try {
            $user = auth()->user();
            $request = request()->validate([
                'name' => [Rule::excludeIf(!strlen(request()->name)),'string'],
                'location' => [Rule::excludeIf(!strlen(request()->location))],
                'lat' => [Rule::excludeIf(!strlen(request()->lat)),'string'],
                'lng' => [Rule::excludeIf(!strlen(request()->lng)),'string'],
                'isModel' => [Rule::excludeIf(!strlen(request()->isModel)),'boolean'],
                'gender' => [Rule::excludeIf(!strlen(request()->gender)),'string'],
                'orientation' => [Rule::excludeIf(!strlen(request()->orientation)),'string'],
                'relationshipStatus' => [Rule::excludeIf(!strlen(request()->relationshipStatus)),'string'],
                'profileType' => [Rule::excludeIf(!strlen(request()->profileType)),'string'],
                'interestTags' => [Rule::excludeIf(!strlen(request()->interestTags)),'string'],
                'birthday' => [Rule::excludeIf(!strlen(request()->birthday)),'string'],
                'occupation' => [Rule::excludeIf(!strlen(request()->occupation)),'string'],
                'wallComments' => [Rule::excludeIf(!strlen(request()->wallComments)),'boolean'],
                'showAge' => [Rule::excludeIf(!strlen(request()->showAge)),'boolean'],
                'bio' => [Rule::excludeIf(!strlen(request()->bio)),'string'],
                'link' => [Rule::excludeIf(!strlen(request()->link)),'string'],
                'gif1' => [Rule::excludeIf(!strlen(request()->gif1)),'string'],
                'gif2' => [Rule::excludeIf(!strlen(request()->gif2)),'string'],
                'deviceId' => [Rule::excludeIf(!strlen(request()->deviceId)),'string'],
                'isOnline' => [Rule::excludeIf(!strlen(request()->isOnline)),'boolean'],
            ]);

            if(request()->deleteWallImage){
                $request['wallpaperUrl']='';
            }
            if(request()->deleteUserImage){
                $request['imageUrl']='';
            }
            // $request['wallpaperUrl']='';
            // $request['imageUrl']='';

            if (request()->hasFile('imageUrl')) {
                //! Using the Storage facade
                $request['imageUrl'] = 'https://' . $_SERVER['SERVER_NAME'] . '/storage/' . request()->imageUrl->store('profileImages', 'public');
            }
            if (request()->hasFile('wallpaperUrl')) {
                //! Using the Storage facade
                $request['wallpaperUrl'] = 'https://' . $_SERVER['SERVER_NAME'] . '/storage/' . request()->wallpaperUrl->store('wallpaperImages', 'public');
            }
            $user->update($request);
            $user = $user->with('settings')
            ->withCount('following')
            ->withCount('followers')
            ->with('comments')
            ->with('channel')
            // ->with('favorites')
            ->find($user->id);

            return response()->json([
                'msg1' => 'updated',
                'msg2' => 'Profile setup is completed',
                'token' => request()->bearerToken(),
                'user' => $user,
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


    public function forgetPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);
            $user = User::where('email', $request->email)->get();

            if ($user->count()) {
                // $token=Str::random(40);
                $token = generateOTP($request->email, now());
                // $domain=URL::to('/');
                // $url= $domain.'/resetPassword?token='.$token;

                // $emailData['url']=$url;
                $emailData['email'] = $request->email;
                $emailData['title'] = 'Password Reset Code is '.$token;
                // $emailData['body']='Click the link below to reset your password';
                $emailData['token'] = $token;
                // Mail::send('emails.forgetPassword',['data'=>$emailData],function($msg) use ($emailData){
                //     $msg->to($emailData['email'])->subject($emailData['title']);
                // });

                PasswordReset::updateOrCreate(
                    ['email' => $request->email],
                    [
                        'email' => $request->email,
                        'token' => $token,
                        'created_at' => now(),

                    ]
                );
                ForgetEmailJob::dispatch($emailData);

                return response()->json([
                    'msg' => 'a code has been to your email to reset your password',
                    'status' => true
                ]);
            } else {
                return response()->json([
                    'msg' => 'User Not Found',
                    'status' => false
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage(),
                'status' => false
            ]);
        }
    }

    // public function resetPasswordLoad(Request $request)
    // {
    //     $resetData = PasswordReset::where('token', $request->token)->first();
    //     // dd($resetData->email);
    //     if(isset($request->token) && $resetData){
    //         $user=User::where('email', $resetData->email)->first();
    //         return view('resetPassword',compact('user'));
    //     }else{
    //         return view("404");
    //     }
    // }
    public function verifyResetToken(Request $request)
    {
        try {
            $request->validate([
                'passwordResetToken' => 'required|string|min:6'
            ]);
            $resetData = PasswordReset::where('token', $request->passwordResetToken)->first();
            if ($resetData) {
                $user = User::where('email', $resetData->email)->first();
                return response()->json([
                    'id' => $user->id,
                    'msg' => 'You are verified!',
                    'status' => true
                ]);
            } else {
                return response()->json([
                    'msg' => 'invalid code',
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

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'password' => 'required|string|min:6',
                'id' => 'required'
            ]);

            $user = User::find($request->id);
            $user->password = bcrypt($request->password);
            $user->save();

            PasswordReset::where('email', $user->email)->delete();


            return response()->json([
                'msg' => 'Password updated',
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




    public function destroy(string $id)
    {
        //
    }
}
