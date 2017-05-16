<?php

namespace App\Api\V1\Controllers;

use Config;
use App\User;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\SignUpRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SignUpController extends Controller
{
    public function signUp(SignUpRequest $request, JWTAuth $JWTAuth)
    {

        $user=User::where('email',$request->email)->first();
        if(count($user)>0){
                return response()->json([
                'success' => false,
                'message'=>'user already exists'
            ]);
        }

        $user = new User($request->all());
        if(!$user->save()) {
                return response()->json([
                'success' => false,
                'message'=>'something bad happened please try again after some time'
            ]);
        }

        if(!Config::get('boilerplate.sign_up.release_token')) {
            return response()->json([
                'success' => true,
                'message'=>'Register Successfully'
            ]);
        }

        $token = $JWTAuth->fromUser($user);
        return response()->json([
            'status' => true,
            'token' => $token
        ]);
    }
}
