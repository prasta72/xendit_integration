<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use mysql_xdevapi\Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserLoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }


        return response()->json([
            'token' => $token,
            'user' => auth()->user()
        ]);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email'],
            'password' => ['required'],
        ]);
    }

    public function testAcces(){
        return 'you logged in';
    }

    public function logout(Request $request)
    {
        // Invalidate the current token and blacklist it
        try {
            // Check if the user is authenticated (token is valid)
            if (auth()->check()) {
                auth()->logout();
                return response()->json(['message' => 'Logout successful'], 200);
            }

            // If the user is not authenticated (token is expired or invalid)
            return response()->json(['message' => 'User is already logged out'], 401);
        } catch (TokenExpiredException $e) {
            // Exception caught when the token is expired
            return response()->json(['message' => 'User is already logged out'], 401);
        } catch (\Exception $e) {
            // Other exceptions that might occur during logout
            return response()->json(['message' => 'Logout failed'], 500);
        }
    }

}
