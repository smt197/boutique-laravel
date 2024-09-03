<?php

namespace App\Http\Controllers;

use App\Enums\StatusResponseEnum;
use App\Http\Requests\RegistreRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Authentication\AuthenticationServiceInterface;
use App\Traits\RestResponseTrait;
use Exception;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use RestResponseTrait;

    protected $authService;

    public function __construct(AuthenticationServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegistreRequest $request)
    {
        try {
            $response = $this->authService->register($request);

            if ($response['success']) {
                return $this->sendResponse([
                    'user' => new UserResource($response['user']),
                    'token' => $response['token'],
                ], StatusResponseEnum::SUCCESS);
            } else {
                return $this->sendResponse(['error' => $response['message']], StatusResponseEnum::ECHEC, $response['status']);
            }
        } catch (Exception $e) {
            return $this->sendResponse(['error' => $e->getMessage()], StatusResponseEnum::ECHEC, 500);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('login', 'password');
        $response = $this->authService->authenticate($credentials);

        if ($response['success']) {
            return response()->json([
                'success' => true,
                'token' => $response['token'],
                'refresh_token' => $response['refresh_token'],
                'user' => new UserResource($response['user']),
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => $response['message'],
            ], 401);
        }
    }

    public function refreshToken(Request $request)
    {
        $credentials = $request->only('login', 'password');
        $response = $this->authService->refreshToken($request);

        if ($response['success']) {
            return response()->json([
                'success' => true,
                'token' => $response['token'],
                'refresh_token' => $response['refresh_token'],
                'user' => new UserResource($response['user']),
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => $response['message']
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $response = $this->authService->logout($user);

        if ($response['success']) {
            return response()->json(['message' => $response['message']]);
        } else {
            return response()->json(['message' => $response['message']], 401);
        }
    }
}
