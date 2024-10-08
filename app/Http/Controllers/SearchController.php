<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

// 'radians(acos(sin(radians('.$lat.')) * sin(radians(lat)) + cos(radians('.$lat.')) * cos(radians(lat)) * cos(radians('.$lng.'-lng))))* 60 * 1.1515 * 1.609344 as distance'
// 'radians(acos(sin(radians('.$lat.')) * sin(radians(lat)) + cos(radians('.$lat.')) * cos(radians(lat)) * cos(radians('.$lng.'-lng))))* 60 * 1.1515 * 1.609344 as distance'
// distance('29.266249','47.940109','29.255445','47.943284','K'),

function distance($lat1, $lon1, $lat2, $lon2, $unit)
{
    if (($lat1 == $lat2) && ($lon1 == $lon2)) {
        return 0;
    } else {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }
}

class SearchController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function searchUserByName(Request $request)
    {
        try {
            $request->validate([
                'search' => 'required|string'
            ]);
            // $user->account()->where('account_no','like','34%')
            $users = User::select('id', 'name', 'imageUrl', 'location', 'isModel', 'link', 'bio')
                ->withCount('following')
                ->withCount('followers')
                ->with([
                    'followers' => function ($query) {
                        $query->select('follower')->where('follower', '=', auth()->user()->id);
                    }
                ])
                ->withCount([
                    'blockers' => function ($query) {
                        $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                    }
                ])
                ->havingNull('blockers_count')
                ->where('name', 'like', "%" . $request->search . "%")
                // ->where('id', '!=', auth()->user()->id)
                ->orderBy('name')
                ->simplePaginate();

            if ($users->count()) {

                return response()->json([
                    'result' => $users,
                    'status' => true
                ]);
            } else {
                return response()->json([
                    'result' => 'no results',
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

    public function searchUserWithFilter(Request $request)
    {
        try {
            $request->validate([
                // 'search' => 'required|string',
                'filter1' => 'string',
            ]);
            $filter1 = $request->filter1;
            $filter2 = $request->filter2;
            $filter3 = $request->filter3;
            // return  response()->json([
            //     'result' => $filter,
            //     'status' => true
            // ]);
            $users = '';
            $offlineUser = '';
            $onlineUser = '';
            $onAndOffUsers = '';
            if ($filter1 == 'following') {

                if ($filter3 == 'noFilter') {
                    if ($filter2 == 'online') {
                        // dd($filter2);
                        $users = auth()->user()
                            ->following()
                            ->withCount('following')
                            ->withCount('followers')
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            ->with([
                                'favoritee' => function ($query) {
                                    $query->select('userId')->where('userId', '=', auth()->user()->id);
                                }
                            ])
                            ->havingNull('blockers_count')
                            // ->where('isOnline', '1')
                            ->orderBy('isOnline', 'desc')
                            // ->orderBy('last_seen', 'asc')
                            ->simplePaginate();
                        $onlineUser = auth()->user()
                            ->following()
                            ->withCount('following')
                            ->withCount('followers')
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            ->with([
                                'favoritee' => function ($query) {
                                    $query->select('userId')->where('userId', '=', auth()->user()->id);
                                }
                            ])
                            ->havingNull('blockers_count')
                            ->where('isOnline', '1')
                            // ->orderBy('isOnline', 'desc')
                            ->orderBy('last_seen', 'asc')
                            ->simplePaginate();
                        $offlineUser = auth()->user()
                            ->following()
                            ->withCount('following')
                            ->withCount('followers')
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            ->with([
                                'favoritee' => function ($query) {
                                    $query->select('userId')->where('userId', '=', auth()->user()->id);
                                }
                            ])
                            ->havingNull('blockers_count')
                            ->where('isOnline', '0')
                            // ->orderBy('isOnline', 'desc')
                            ->orderBy('last_seen', 'asc')
                            ->simplePaginate();
                    } elseif ($filter2 == 'popular') {
                        $users = auth()->user()
                            ->following()
                            ->withCount('following')
                            ->withCount('followers')
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            ->with([
                                'favoritee' => function ($query) {
                                    $query->select('userId')->where('userId', '=', auth()->user()->id);
                                }
                            ])
                            // ->where('users.id','!=', '1')
                            ->havingNull('blockers_count')
                            // ->having('followers_count', '>', 10)

                            ->orderBy('followers_count', 'desc')
                            ->simplePaginate();
                        // $users->data=$users->data;
                    } elseif ($filter2 == 'new') {
                        $users = auth()->user()
                            ->following()
                            ->withCount('following')
                            ->withCount('followers')
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            ->with([
                                'favoritee' => function ($query) {
                                    $query->select('userId')->where('userId', '=', auth()->user()->id);
                                }
                            ])
                            ->havingNull('blockers_count')
                            // ->where('users.created_at','like', "%".date("Y-m-d")."%")

                            ->orderBy('created_at', 'desc')
                            ->simplePaginate();
                    } elseif ($filter2 == 'nearby') {
                        $users = User::select('id', 'name', 'imageUrl', 'profileType', 'isOnline', 'created_at')
                            ->with([
                                'following' => function ($query) {
                                    $lat = auth()->user()->lat;
                                    $lng = auth()->user()->lng;
                                    $query->select('followee as id', 'name', 'imageUrl', 'location', 'link', 'bio', 'lat', 'lng', 'profileType', 'isOnline', 'users.created_at', DB::raw('ATAN2(SQRT(pow(cos(lat) * sin(lng-' . $lng . '), 2) +
                                    pow(cos(' . $lat . ') * sin(lat) - sin(' . $lat . ') * cos(lat) * cos(lng-' . $lng . '), 2)),sin(' . $lat . ') * sin(lat) + cos(' . $lat . ') * cos(lat) * cos(lng-' . $lng . '))*6371000/1609.344  as distance'))
                                        ->withCount('following')
                                        ->withCount('followers')
                                        ->with([
                                            'favoritee' => function ($query) {
                                                $query->select('userId')->where('userId', '=', auth()->user()->id);
                                            }
                                        ])
                                        ->withCount([
                                            'blockers' => function ($query) {
                                                $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                            }
                                        ])
                                        ->havingNull('blockers_count');
                                    // ->having('distance', '<', 10);
                                    // ->orderBy('followee')
                                    // ->simplePaginate();
                                }
                            ])->find(auth()->user()->id);
                        $users['data'] = $users->following;
                    } else {
                        $users = auth()->user()
                            ->following()
                            ->withCount('following')
                            ->withCount('followers')
                            ->with([
                                'followers' => function ($query) {
                                    $query->select('follower')->where('follower', '=', auth()->user()->id);
                                }
                            ])
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            ->with([
                                'favoritee' => function ($query) {
                                    $query->select('userId')->where('userId', '=', auth()->user()->id);
                                }
                            ])
                            ->havingNull('blockers_count')
                            ->orderBy('name')
                            ->simplePaginate();
                    }

                } else {
                    if ($filter2 == 'online') {
                        // dd($filter2);
                        $users = auth()->user()
                            ->following()
                            ->withCount('following')
                            ->withCount('followers')
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            ->with([
                                'favoritee' => function ($query) {
                                    $query->select('userId')->where('userId', '=', auth()->user()->id);
                                }
                            ])
                            ->havingNull('blockers_count')
                            ->where('profileType', $filter3)
                            // ->where('profileType', $filter3)
                            ->orderBy('isOnline', 'desc')
                            // ->orderBy('last_seen', 'asc')
                            ->simplePaginate();
                        $onlineUser = auth()->user()
                            ->following()
                            ->withCount('following')
                            ->withCount('followers')
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            ->with([
                                'favoritee' => function ($query) {
                                    $query->select('userId')->where('userId', '=', auth()->user()->id);
                                }
                            ])
                            ->havingNull('blockers_count')
                            ->where('profileType', $filter3)
                            ->where('isOnline', '1')
                            // ->orderBy('isOnline', 'desc')
                            ->orderBy('last_seen', 'asc')
                            ->simplePaginate();
                        $offlineUser = auth()->user()
                            ->following()
                            ->withCount('following')
                            ->withCount('followers')
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            ->with([
                                'favoritee' => function ($query) {
                                    $query->select('userId')->where('userId', '=', auth()->user()->id);
                                }
                            ])
                            ->havingNull('blockers_count')
                            ->where('profileType', $filter3)
                            ->where('isOnline', '0')
                            // ->orderBy('isOnline', 'desc')
                            ->orderBy('last_seen', 'asc')
                            ->simplePaginate();
                    } elseif ($filter2 == 'popular') {
                        $users = User::find(auth()->user()->id)
                            ->following()
                            ->withCount('following')
                            ->withCount('followers')
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            ->with([
                                'favoritee' => function ($query) {
                                    $query->select('userId')->where('userId', '=', auth()->user()->id);
                                }
                            ])
                            ->havingNull('blockers_count')
                            // ->having('followers_count', '>', 10)
                            ->where('profileType', $filter3)
                            ->orderBy('followers_count', 'desc')
                            ->simplePaginate();
                    } elseif ($filter2 == 'new') {
                        $users = auth()->user()
                            ->following()
                            ->withCount('following')
                            ->withCount('followers')
                            ->with([
                                'followers' => function ($query) {
                                    $query->select('follower')->where('follower', '=', auth()->user()->id);
                                }
                            ])
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            ->with([
                                'favoritee' => function ($query) {
                                    $query->select('userId')->where('userId', '=', auth()->user()->id);
                                }
                            ])
                            ->havingNull('blockers_count')
                            // ->whereDate('users.created_at', '=', now())
                            ->where('profileType', $filter3)
                            ->orderBy('created_at', 'desc')
                            ->simplePaginate();
                    } elseif ($filter2 == 'nearby') {
                        $users = User::select('id', 'name', 'imageUrl', 'profileType', 'created_at')
                            ->with([
                                'following' => function ($query) {
                                    $lat = auth()->user()->lat;
                                    $lng = auth()->user()->lng;
                                    $query->select('followee as id', 'name', 'imageUrl', 'location', 'link', 'bio', 'lat', 'lng', 'profileType', 'users.created_at', DB::raw('ATAN2(SQRT(pow(cos(lat) * sin(lng-' . $lng . '), 2) +
                                    pow(cos(' . $lat . ') * sin(lat) - sin(' . $lat . ') * cos(lat) * cos(lng-' . $lng . '), 2)),sin(' . $lat . ') * sin(lat) + cos(' . $lat . ') * cos(lat) * cos(lng-' . $lng . '))*6371000/1609.344  as distance'))
                                        ->withCount('following')
                                        ->withCount('followers')
                                        ->with([
                                            'favoritee' => function ($query) {
                                                $query->select('userId')->where('userId', '=', auth()->user()->id);
                                            }
                                        ])
                                        ->withCount([
                                            'blockers' => function ($query) {
                                                $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                            }
                                        ])
                                        ->havingNull('blockers_count')
                                        // ->having('distance', '<', 100)
                                        ->where('profileType', request()->filter3);

                                    // ->orderBy('followee')
                                    // ->simplePaginate();
                                }
                            ])->find(auth()->user()->id);
                        $users['data'] = $users->following;
                    } else {
                        $users = auth()->user()
                            ->following()
                            ->withCount('following')
                            ->withCount('followers')
                            ->with([
                                'followers' => function ($query) {
                                    $query->select('follower')->where('follower', '=', auth()->user()->id);
                                }
                            ])
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            ->with([
                                'favoritee' => function ($query) {
                                    $query->select('userId')->where('userId', '=', auth()->user()->id);
                                }
                            ])
                            ->havingNull('blockers_count')
                            ->where('profileType', $filter3)
                            ->orderBy('name')
                            ->simplePaginate();
                    }
                }



            } else {

                if ($filter3 == 'noFilter') {
                    if ($filter2 == 'online') {
                        $users = User::select('id', 'name', 'imageUrl', 'location', 'link', 'bio', 'isOnline', 'profileType', 'created_at', 'last_seen')
                            ->withCount('following')
                            ->withCount('followers')
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
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            // ->where('id', '!=', auth()->user()->id)
                            ->havingNull('blockers_count')
                            ->where('isOnline', '1')
                            // ->orderBy('isOnline','desc')
                            // ->orderBy('last_seen', 'asc')
                            ->simplePaginate();
                        $onlineUser = User::select('id', 'name', 'imageUrl', 'location', 'link', 'bio', 'isOnline', 'profileType', 'created_at', 'last_seen')
                            ->withCount('following')
                            ->withCount('followers')
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
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            // ->where('id', '!=', auth()->user()->id)
                            ->havingNull('blockers_count')
                            ->where('isOnline', '1')
                            ->orderBy('name','asc')
                            ->simplePaginate();
                        $offlineUser = User::select('id', 'name', 'imageUrl', 'location', 'link', 'bio', 'isOnline', 'profileType', 'created_at', 'last_seen')
                            ->withCount('following')
                            ->withCount('followers')
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
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            // ->where('id', '!=', auth()->user()->id)
                            ->havingNull('blockers_count')
                            ->where('isOnline', '0')
                            // ->orderBy('isOnline','desc')
                            ->orderBy('last_seen', 'desc')
                            ->simplePaginate();
                        $onAndOffUsers = array_merge($onlineUser->items(), $offlineUser->items());
                    } elseif ($filter2 == 'popular') {
                        $users = User::select('id', 'name', 'imageUrl', 'location', 'link', 'bio', 'profileType', 'isOnline', 'created_at')
                            ->withCount('following')
                            ->withCount('followers')
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
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            ->havingNull('blockers_count')
                            // ->where('id', '!=', auth()->user()->id)
                            // ->having('followers_count', '>', 10)
                            ->orderBy('followers_count', 'desc')
                            ->simplePaginate();
                    } elseif ($filter2 == 'new') {
                        $users = User::select('id', 'name', 'imageUrl', 'location', 'link', 'bio', 'profileType', 'isOnline', 'created_at')
                            ->withCount('following')
                            ->withCount('followers')
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
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            ->havingNull('blockers_count')
                            // ->where('id', '!=', auth()->user()->id)
                            // ->whereDate('created_at', '=', now())
                            ->orderBy('created_at', 'desc')
                            ->simplePaginate();
                    } elseif ($filter2 == 'nearby') {
                        $lat = auth()->user()->lat;
                        $lng = auth()->user()->lng;
                        $users = User::select('id', 'name', 'imageUrl', 'location', 'link', 'bio', 'lat', 'lng', 'profileType', 'isOnline', 'created_at', DB::raw('ATAN2(SQRT(pow(cos(lat) * sin(lng-' . $lng . '), 2) +
                        pow(cos(' . $lat . ') * sin(lat) - sin(' . $lat . ') * cos(lat) * cos(lng-' . $lng . '), 2)),sin(' . $lat . ') * sin(lat) + cos(' . $lat . ') * cos(lat) * cos(lng-' . $lng . '))*6371000/1609.344  as distance'))
                            ->withCount('following')
                            ->withCount('followers')
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
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            ->havingNull('blockers_count')
                            // ->where('id', '!=', auth()->user()->id)
                            // ->having('distance', '<', 100)
                            ->orderBy('name')
                            ->simplePaginate();
                    } else {
                        $users = User::select('id', 'name', 'imageUrl', 'location', 'isModel', 'link', 'bio', 'isOnline', 'profileType', 'created_at')
                            ->withCount('following')
                            ->withCount('followers')
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
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            ->havingNull('blockers_count')
                            // ->where('id', '!=', auth()->user()->id)
                            ->orderBy('name')
                            ->simplePaginate();
                    }

                } else {
                    if ($filter2 == 'online') {
                        $users = User::select('id', 'name', 'imageUrl', 'location', 'link', 'bio', 'isOnline', 'profileType', 'created_at', 'last_seen')
                            ->withCount('following')
                            ->withCount('followers')
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
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            ->havingNull('blockers_count')
                            // ->where('id', '!=', auth()->user()->id)
                            ->where('profileType', $filter3)
                            // ->where('profileType', $filter3)
                            ->orderBy('isOnline', 'desc')
                            // ->orderBy('last_seen', 'asc')
                            ->simplePaginate();
                        $onlineUser = User::select('id', 'name', 'imageUrl', 'location', 'link', 'bio', 'isOnline', 'profileType', 'created_at', 'last_seen')
                            ->withCount('following')
                            ->withCount('followers')
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
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            ->havingNull('blockers_count')
                            // ->where('id', '!=', auth()->user()->id)
                            ->where('isOnline', '1')
                            ->where('profileType', $filter3)
                            ->orderBy('name', 'asc')
                            // ->orderBy('last_seen', 'asc')
                            ->simplePaginate();
                        $offlineUser = User::select('id', 'name', 'imageUrl', 'location', 'link', 'bio', 'isOnline', 'profileType', 'created_at', 'last_seen')
                            ->withCount('following')
                            ->withCount('followers')
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
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            ->havingNull('blockers_count')
                            // ->where('id', '!=', auth()->user()->id)
                            ->where('isOnline', '0')
                            ->where('profileType', $filter3)
                            // ->orderBy('isOnline', 'asc')
                            ->orderBy('last_seen', 'desc')
                            ->simplePaginate();
                        $onAndOffUsers = array_merge($onlineUser->items(), $offlineUser->items());
                    } elseif ($filter2 == 'popular') {
                        $users = User::select('id', 'name', 'imageUrl', 'location', 'link', 'bio', 'profileType', 'isOnline', 'created_at')
                            ->withCount('following')
                            ->withCount('followers')
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
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            ->havingNull('blockers_count')
                            // ->where('id', '!=', auth()->user()->id)
                            // ->having('followers_count', '>', 10)
                            ->where('profileType', $filter3)
                            ->orderBy('followers_count', 'desc')
                            ->simplePaginate();
                    } elseif ($filter2 == 'new') {
                        $users = User::select('id', 'name', 'imageUrl', 'location', 'link', 'bio', 'profileType', 'isOnline', 'created_at')
                            ->withCount('following')
                            ->withCount('followers')
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
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            ->havingNull('blockers_count')
                            // ->where('id', '!=', auth()->user()->id)
                            // ->whereDate('created_at', '=', now())
                            ->where('profileType', $filter3)
                            ->orderBy('created_at', 'desc')
                            ->simplePaginate();
                    } elseif ($filter2 == 'nearby') {
                        $lat = auth()->user()->lat;
                        $lng = auth()->user()->lng;
                        $users = User::select('id', 'name', 'imageUrl', 'location', 'link', 'bio', 'lat', 'lng', 'profileType', 'isOnline', 'created_at', DB::raw('ATAN2(SQRT(pow(cos(lat) * sin(lng-' . $lng . '), 2) +
                        pow(cos(' . $lat . ') * sin(lat) - sin(' . $lat . ') * cos(lat) * cos(lng-' . $lng . '), 2)),sin(' . $lat . ') * sin(lat) + cos(' . $lat . ') * cos(lat) * cos(lng-' . $lng . '))*6371000/1609.344  as distance'))
                            ->withCount('following')
                            ->withCount('followers')
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
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            ->havingNull('blockers_count')
                            // ->where('id', '!=', auth()->user()->id)
                            // ->having('distance', '<', 100)
                            ->where('profileType', $filter3)
                            ->orderBy('name')
                            ->simplePaginate();
                    } else {
                        $users = User::select('id', 'name', 'imageUrl', 'location', 'isModel', 'link', 'bio', 'isOnline', 'profileType', 'isOnline', 'created_at')
                            ->withCount('following')
                            ->withCount('followers')
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
                            ->withCount([
                                'blockers' => function ($query) {
                                    $query->select('blocker')->where('blocker', '=', auth()->user()->id);
                                }
                            ])
                            ->havingNull('blockers_count')
                            ->where('profileType', $filter3)
                            // ->where('id', '!=', auth()->user()->id)
                            ->orderBy('name')
                            ->simplePaginate();
                    }
                }



            }




            // followers_count
            if ($users->count()) {
                return response()->json([
                    'filters' => [$filter1, $filter2, $filter3],
                    'result' => $users,
                    'onAndOffUsers' => $onAndOffUsers,
                    // 'offlineUser' => $offlineUser,
                    'results' => true,
                    'status' => true,
                ]);
            } else {
                return response()->json([
                    'filters' => [$filter1, $filter2, $filter3],
                    'result' => $users,
                    'msg' => 'no results',
                    'results' => false,
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
