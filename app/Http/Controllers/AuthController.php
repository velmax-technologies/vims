<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponseFormatTrait;
use Illuminate\Database\QueryException;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use Modules\User\Transformers\UserResource;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    use ApiResponseFormatTrait;

    public function login(LoginRequest $request)
    {
        $request->validated();

        $credentials = [];
        $credentials['username'] = $request->username;
        $credentials['password'] = $request->password;
        $credentials = $request->only('username', 'password');
        try {
            if (!$token = Auth::attempt($credentials)) {
                return $this->unauthorizedResponse();
            }
            
            activity('login')->causedBy(Auth::user())
            ->log('User ' . Auth::user()->username . ' logged in successfully');

            return new LoginResource($token);
        } catch (QueryException $queryException) {
            return $this->queryExceptionResponse($queryException);
        } catch (Exception $exception) {
            $this->recordException($exception);
            return $this->serverErrorResponse($exception);
        }
    }

    public function profile()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->unauthorizedResponse();
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Invalid token'], 400);
        }

        return (new UserResource($user))->additional($this->preparedResponse('show'));
    }

    public function logout()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->unauthorizedResponse();
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Invalid token'], 400);
        }

        JWTAuth::invalidate(JWTAuth::getToken());
        
        activity('logout')->causedBy(Auth::user())->log('User ' . $user->username . ' logged out successfully');

        return $this->logoutResponse();
    }
    
}
