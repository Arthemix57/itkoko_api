<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:12',
            'sexe' => 'required|string|max:6',
            'phone' => 'required|numeric|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:6',
            ],
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $request['password'] = Hash::make($request['password']);
        $request['name'] = ucwords(Str::lower($request['name']));
        $request['remember_token'] = Str::random(10);
        $request['phone'] = $request['phone'];
        $request['sexe'] = $request['sexe'] ? $request['sexe'] : 'Homme';
        $request['totalpt'] = $request['totalpt'] ? $request['totalpt'] : 0;
        $request['type'] = $request['type'] ? $request['type']  : 0;
        $user = User::create($request->toArray());
        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        $response = ['token' => $token];

        return response(['user' => $user, 'access_token' => $response]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $user = User::whereEmail($request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                $response = ['token' => $token];
                $message = 'success';
                return response(['user' => $user, 'access_token' => $response, 'message' => $message]);
            } else {
                $response = ["message" => "mot de passe incorrect"];
                return response($response, 422);
            }
        } else {
            $response = ["message" => "cette utilisateur n'existe pas"];
            return response($response, 422);
        }
    }

    public function editUser(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if ($request->name) $user->name = $request->name;
        if ($request->phone) $user->phone = $request->phone;
        if ($request->sexe) $user->sexe = $request->sexe;
        if ($request->email) $user->email = $request->email;
        if ($request->totalpt) $user->totalpt = $request->totalpt;

        $user->update();
        return response()->json(['message' => $user['avatar']]);
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }
}
