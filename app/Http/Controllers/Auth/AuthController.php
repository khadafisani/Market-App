<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\AddUserRequest;

use App\Http\Controllers;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $valid = $request->validated();

        $data = $request->only(['email', 'password']);

        if(!Auth::attempt($data)){
            return response()->json([
                'status' => 'failed',
                'code' => 400,
                'message' => 'Username Or password incorrect',
                'data' => null
            ]);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        $token->save();

        return response()->json([
            'status' => 'ok',
            'code' => 200,
            'message' => '',
            'data' => [
            'token_bearer' => 'Bearer '.$tokenResult->accessToken,
            'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString(),
            ],
        ]);
    }

    public function addUser(AddUserRequest $request)
    {
        $valid = $request->validate();

        $user = new User; //new user object

        //user data
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->role = $request->role;

        //insert to database
        $user->save();

        return response()->json([
            'status' => 'oke',
            'code' => 201,
            'message' => 'Successfuly created user!',
            'data' => null,
        ]);
    }

    public function users(Request $request)
    {
       return response()->json([
           'status' => 'ok',
           'message' => '',
           'code' => 200,
           'data' => User::all()]);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);

        if($user)
        {
            $user->password = $request->password;
            $user->role = $request->role;
            $user->save();

            $data = [
                'status' => 'ok',
                'code' => 200,
                'message' => 'user successfully update',
                'data' => null
            ];
        }
        else
        {
            $data = [
                'status' => 'failed',
                'code' => 400,
                'message' => 'user not found',
                'data' => null
            ];
        }

        return response()->json($data);
    }
}
