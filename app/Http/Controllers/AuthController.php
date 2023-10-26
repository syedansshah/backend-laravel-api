<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAuthRequest;
use App\Http\Requests\UserLoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt', ['except' => ['signup', 'login']]);
    }
    public function signup(UserAuthRequest $request)
    {
        try {
            $user = User::where(['email' => $request->email])->first();
            if ($user) {
                return sendError('Error.', "User already exist. Login required.", 422);
            } else {
                $user = User::create([
                    'name' => $request->username,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
                return sendResponse('User registered successfully.', $user, 400);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return sendError('Error.', $e->getMessage(), 500);
        }
    }

    public function login(UserLoginRequest $request)
    {
        try {
            $user = User::where(['email' => $request->email])->first();
            if (!$user) {
                return sendError('Error.', "User not found. Signup required.", 422);
            } else {
                $token = JWTAuth::attempt($request->all());
                if ($token) {
                    $user = Auth::user();
                    return sendAuthResponse('User logged in successfully.', $user, $token, 400);
                } else {
                    return sendError('Error.', 'Unauthorize user', 401);
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return sendError('Error.', $e->getMessage(), 500);
        }
    }
}
