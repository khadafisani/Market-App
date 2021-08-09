<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Http\Controllers;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->only(['email', 'password']);
        $valid = Validator::make($data, [
            'email'     => 'required|email',
            'password'  => 'required|string',
        ]);

        if($valid->fails())
        {
            return response()->json($valid->errors(), 422); //422 is any error response
        }

        if(!Auth::attempt($data)){
            return response()->json(['message' => 'Username Or password incorrect'], 403);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        $token->save();

        return response()->json([
            'token_bearer'  => 'Bearer '.$tokenResult->accessToken,
            'expires_at'    => Carbon::parse($token->expires_at)->toDateTimeString()
        ]);
    }

    public function register(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'name'      => 'required',
            'email'     => 'required|email',
            'password'  => 'required|string',
            'role'      => 'required|string',
        ]);

        if($valid->fails())
        {
            return response()->json($valid->errors(), 422); //422 error response
        }

        $user = new User; //new user object

        //user data
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->role = $request->role;

        //insert to database
        $user->save();

        return response()->json(['message' => 'Successfuly created user!'], 201);
    }

    public function user(Request $request)
    {
       return response()->json(['data' => $request->user(), 201]);
    }
}
