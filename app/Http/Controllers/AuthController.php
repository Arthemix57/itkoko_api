<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Code;
use App\Jobs\SendCodeJob;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\ResetPasswordJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //
    function __construct()
    {
        $this->middleware('auth:api', ['except' => ['register', 'login', 'sendmail', 'send', 'valitated', 'resetpassword']]);
    }

    public function __invoke()
    {
        return response()->json(['message' => 'Welcome to the API']);
    }

    public function index()
    {
        $users = User::all();
        return response()->json(['users' => $users]);
    }

    public function getallusers()
    {
        $users = User::where('type', 0)->get();
        $servers = User::where('type', 1)->get();
        return response()->json(['servers' => $servers, 'users' => $users]);
    }

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
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
        $user = User::create($request->toArray());
        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        $response = ['token' => $token];

        return response(['user' => $user, 'access_token' => $response]);
    }

    public function registerserver(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
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
        $request['is_active'] = $request->has('is_active') ? $request->is_active : true; // Default to true if not provided
        $request['sexe'] = $request['sexe'] ? $request['sexe'] : 'Homme';
        $request['totalpt'] = $request['totalpt'] ? $request['totalpt'] : 0;
        $request['type'] = 1;
        $user = User::create($request->toArray());
        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        $response = ['token' => $token];

        return response(['user' => $user, 'access_token' => $response]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifiant' => ['required', function ($attribute, $value, $fail) {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL) && !preg_match('/^\+?[0-9]{7,15}$/', $value)) {
                $fail('Le champ doit être un email valide ou un numéro de téléphone.');
            }
        }],
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        };

        $phone = null;
        $email = null;
        if(preg_match('/^\+?[0-9]{7,15}$/', $request->identifiant)) {
            $phone = preg_replace('/\D/', '', $request->identifiant); // Extract digits from phone
        } else {
            $email = filter_var($request->identifiant, FILTER_VALIDATE_EMAIL) ? $request->identifiant : null;
        }
        $user = User::where(function ($query) use ($phone, $email) {
            if ($phone) {
                $query->where('phone', $phone);
            }
            if ($email) {
                $query->whereEmail($email);
            }
        })->first();
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
            $response = ["message" => "cet utilisateur n'existe pas"];
            return response($response, 422);
        }
    }

    /* public function loginServeur(Request $request)
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
            if ($user->type == 1) {
                if (Hash::check($request->password, $user->password)) {
                    $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                    $response = ['token' => $token];
                    $message = 'success';
                    $user->online = true;
                    $user->update();
                    return response(['user' => $user, 'access_token' => $response, 'message' => $message]);
                } else {
                    $response = ["message" => "mot de passe incorrect"];
                    return response($response, 422);
                }
            } else {
                $response = ["message" => 'accès réservé au personnel'];
                return response($response, 422);
            }
        } else {
            $response = ["message" => "cette utilisateur n'existe pas"];
            return response($response, 422);
        }
    } */

    /* public function loginAdmin(Request $request)
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
            if ($user->type == 2) {
                if (Hash::check($request->password, $user->password)) {
                    $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                    $response = ['token' => $token];
                    $message = 'success';
                    $user->online = true;
                    $user->update();
                    return response(['user' => $user, 'access_token' => $response, 'message' => $message]);
                } else {
                    $response = ["message" => "mot de passe incorrect"];
                    return response($response, 422);
                }
            } else {
                $response = ["message" => 'accès réservé au personnel'];
                return response($response, 422);
            }
        } else {
            $response = ["message" => "cette utilisateur n'existe pas"];
            return response($response, 422);
        }
    } */

    public function editIsActive($id)
    {
        $user = User::find($id);
        if ($user) {
            if ($user->type == 1) {
                $user->is_active = !$user->is_active; // Default to true if not provided
                if ($user->is_active) {
                    $message = 'serveur(se) actif(ve)';
                } else {
                    $message = 'serveur(se) desactivé(e)';
                }
                $user->update();
                return response()->json(['message' => $message, 'user' => $user]);
            } else {
                return response()->json(['message' => 'accès réservé au personnel'], 403);
            }
        } else {
            return response()->json(['message' => 'utilisateur non trouve'], 404);
        }
    }

    public function editUser(Request $request)
    {
        $user = User::find(Auth::user()->id)->first();
        if ($request->name) $user->name = $request->name;
        if ($request->phone) $user->phone = $request->phone;
        if ($request->sexe) $user->sexe = $request->sexe;
        if ($request->email) $user->email = $request->email;
        if ($request->totalpt) $user->totalpt = $request->totalpt;

        $user->update();
        return response()->json(['message' => $user]);
    }

    public function user()
    {
        $user = User::find(Auth::user()->id)->first();
        return response()->json([$user]);
    }

    public function deletedUser($id)
    {
        User::find($id)->delete();
        return response()->json(['message' => 'utilisateur supprimer']);
    }

    // envoyer les mails a un user ayant deja un compte
    public function sendmail(Request $request)
    {
        $user = User::whereEmail($request->email)->first();
        $code = rand(1542, 9999);
        if ($user) {
            $codelink = Code::where('email', $request->email)->first();
            if ($codelink) {
                Code::where('email', $request->email)->update(['code' => $code]);
            } else {
                Code::create(['email' => $request->email, 'code' => $code]);
            }

            SendCodeJob::dispatch($user, $code);
            return response()->json([
                'message' => true,
                'user' => $user,
            ]);
        } else {
            return response()->json([
                'message' => "Aucun compte trouve"
            ], 422);
        }
    }


    // pour la verification de l'adresse mail
    public function send(Request $request)
    {

        $code = rand(2657, 9999);
        //Mail::to($request->email)->send(new ResetPasswordMail($code));
        $preuv = Code::where("email", $request->email)->first();
        if ($preuv) {
            Code::where("email", $request->email)->update(['code' => $code]);
        } else {
            $codelink = new Code();
            $codelink->email = $request->email;
            $codelink->code = $code;
            $codelink->save();
        }

        return response()->json(['message' => 'updated successfully']);
    }

    public function valitated(Request $request)
    {
        $line = Code::where("code", $request->code)->where("email", $request->email)->first();
        if ($line) {
            return response()->json([
                'message' => true,
                "code" => request('code'),
                "email" => request('email'),
            ]);
        }
        return response()->json([
            'message' => false,
            "code" => request('code'),
            "email" => request('email'),
        ], 422);
    }

    public function resetpassword(Request $request, $id)
    {
        User::find($id)->update(['password' => Hash::make(request('password'))]);
        ResetPasswordJob::dispatch(User::find($id));
        return response(['message' => 'updated successfully']);
    }

    public function logout(Request $request)
    {
        User::find(Auth::user()->id)->first();
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }
}
