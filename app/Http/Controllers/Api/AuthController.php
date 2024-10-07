<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return $this->error([], 'Credentials not match', 401);
        }

        return $this->success([
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('API Token')->plainTextToken,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return $this->success([
            'token' => $user->createToken('authToken')->plainTextToken,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request)
    {
        return $this->success([
            'user' => $request->user(),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return $this->success([], 'Successfully logged out');
        } catch (Exception $exception) {
            return $this->success([], 'Successfully logged out');
        }
    }
}
