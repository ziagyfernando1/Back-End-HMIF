<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function login(Request $request){
        $credentials = $request->only('email', 'password');
        
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        $member = new MemberController();
        $user = $member->get_member();
        $res    = $this->res(true, null, $user, $token);

        return response()->json($res);
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'nim' => 'required|string|max:255|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'key_pass' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        };
        
        $keyPass = $request->get('key_pass');
        
        if($keyPass != "punyaHMIF"){
            $msg    = "Key Pass Failed";
            $res    = $this->res(false, $msg);
            return response()->json($res, 401);
        };

        $user = User::create([
            'nim' => $request->get('nim'),
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $userRes    = [
            'member_nim' => $user['nim'],
            'member_name' => $user['name'],
            'member_email' => $user['email'],
        ];

        $token      = JWTAuth::fromUser($user);
        $res        = $this->res(true, null, $userRes, $token);
        
        return response()->json($res, 201);
    }

    public function refreshToken(Request $request)
    {
        $refreshToken = JWTAuth::refresh(JWTAuth::getToken());
        
        $user = JWTAuth::setToken($refreshToken)->toUser();
        
        $request->headers->set('Authorization', 'Bearer ' . $refreshToken);

        $res = $this->res(true, null, $user, $refreshToken);
        
        return response()->json($res, 200);
    }

    public function res($success, $message = null, $user = null, $token = null){
        $res = [];
        
        if ($success){
            $res = [
                'success' => $success,
                'user' => $user,
                'token' => $token,
            ];
        } else {
            $res = [
                'success' => $success,
                'message' => $message,
            ];
        }

        return $res;
    }
}
