<?php

namespace Api\Controllers;

use App\User;
use Dingo\Api\Facade\API;
use Illuminate\Http\Request;
use Api\Requests\UserRequest;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends BaseController
{
    public function me(Request $request)
    {
        return JWTAuth::parseToken()->authenticate();
    }

    /**
     * Login as an existing user - generate and return a new JWT
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(Request $request)
    {
        // grab credentials from the request
        $credentials = [
            'email' => $request->getUser(),
            'password' => $request->getPassword(),
        ];

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        $responseData = [
            'token' => $token,
            'data' => JWTAuth::getPayload($token)->toArray() + ['user' => JWTAuth::toUser($token)],
        ];

        // all good so return the token
        return response()->json($responseData);
    }

    /**
     * Refresh an existing valid JWT - generate and return the new JWT, invalidate the old one
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken()
    {
        $token = JWTAuth::getToken();
        if (!$token) {
            return response()->json(['error' => 'token_not_provided'], 400);
        }

        try {
            $token = JWTAuth::refresh($token);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'token_invalid', 400]);
        }

        $responseData = [
            'token' => $token,
            'data' => JWTAuth::getPayload($token)->toArray() + ['user' => JWTAuth::toUser($token)],
        ];

        return response()->json($responseData);
    }

    /**
     * Get an "anonymous" JWT not linked to a user - take an existing valid JWT and generate return a new one
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAnonymousToken()
    {
        $token = JWTAuth::getToken();
        if (!$token) {
            return response()->json(['error' => 'token_not_provided'], 400);
        }

        try {
            $user = JWTAuth::toUser($token);
            $token = JWTAuth::refresh($token);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'token_invalid', 400]);
        }

        $responseData = ['token' => $token, 'data' => JWTAuth::getPayload($token)->toArray()];
        //if token is linked to user, return user data
        if (!empty($user)) {
            $responseData['data']['user'] = JWTAuth::toUser($token);
        }

        return response()->json($responseData);
    }

    public function validateToken() 
    {
        // Our routes file should have already authenticated this token, so we just return success here
        return API::response()->array(['status' => 'success'])->statusCode(200);
    }

    public function register(UserRequest $request)
    {
        $newUser = [
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
        ];
        $user = User::create($newUser);
        $token = JWTAuth::fromUser($user);

        return response()->json(compact('token'));
    }
}