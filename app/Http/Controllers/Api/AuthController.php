<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\LoginResource;
use App\Http\Resources\RegisterResource;
use App\Http\Trait\HttpResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Class AuthController
 * Handles user authentication operations such as registration and login.
 *
 * @package App\Http\Controllers\Api
 */
class AuthController extends Controller
{
    use HttpResponse;

    /**
     * Register a new user and generate an access token.
     *
     * @param RegisterRequest $request The validated registration data.
     * @return JsonResponse A JSON response containing user data and access token.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'customer',
        ]);

        $token = $user->createToken('Access Token')->accessToken;

        return $this->success(new RegisterResource($user, $token), 'Kayıt işleminiz başarıyla tamamlandı.')
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Authenticate a user and return an access token.
     *
     * @param LoginRequest $request The validated login credentials.
     * @return JsonResponse A JSON response containing user data and access token or error message.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->accessToken;

        return $this->success(new LoginResource($user, $token), 'Giriş başarılı.')
            ->setStatusCode(Response::HTTP_OK);
    }
}
