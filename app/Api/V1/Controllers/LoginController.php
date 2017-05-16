<?php

namespace App\Api\V1\Controllers;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request, JWTAuth $JWTAuth)
    {
        $credentials = $request->only(['email', 'password']);

        try {
            $token = $JWTAuth->attempt($credentials);

            if(!$token) {
            return response()
                ->json([
                    'success' =>false,
                    'message' => "please check your crendentials"
                ]);
            }

        } catch (JWTException $e) {
            throw new HttpException(500);
        }


        return response()
            ->json([
                'success' =>true,
                'user'=>Auth::user(),
                'token' => $token
            ]);
    }
}
