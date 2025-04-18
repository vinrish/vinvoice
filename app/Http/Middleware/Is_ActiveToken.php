<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\OauthAccessTokens;

class Is_ActiveToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $userSerialize = serialize($user);
        $userUnserializeArray = (array) unserialize($userSerialize);

        $arrayKeys = array_keys($userUnserializeArray);
        foreach ($arrayKeys as $value)
        {

            if (strpos($value, 'Stocky_token') !== false) {

                $userAccessTokenArray = (array) $userUnserializeArray[$value];
                $arrayAccessKeys = array_keys($userAccessTokenArray);
                foreach ($arrayAccessKeys as $arrayAccessValue) {

                    if (strpos($arrayAccessValue, 'original') !== false) {

                        $userTokenId = $userAccessTokenArray[$arrayAccessValue]['id'];
                        $checkToken = OauthAccessTokens::where([
                            ['id', '=', $userTokenId],
                            ['expires_at', '>', Carbon::now()]
                        ])->first();

                        if ( !$checkToken ) {
                            return response()->json([
                                'error'=>true,
                                'msg'=> 'Token time has expired. Please log in again.'
                            ]);
                        }
                    }
                }
            }
        }

        return $next($request);
    }
}
