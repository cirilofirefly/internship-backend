<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\Auth\RememberToken;
use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Socialite\Contracts\User as ContractsUser;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{

    public function signin($method)
    {
        return Socialite::driver($method)->redirect();
    }

    public function facebookCallback()
    {
        $data = $this->registerOrLogin(Socialite::driver('facebook')->user());
        $remember_token = $this->storeRememberToken($data);
        return Redirect::away("http://localhost:8080/login?id=" . Crypt::encrypt($remember_token->id));
    }

    public function getRememberToken(Request $request) {
        try {
            $id = Crypt::decrypt($request->id);
            $remember_token = RememberToken::where('id', $id)->first();
            if($remember_token) {
                $data = [
                    'user'          => $remember_token->getUser,
                    'access_token'  => $remember_token->access_token
                ];
                $remember_token->delete();
                return response()->json($data, 200);
            }
        } catch (DecryptException $e) {
            return response()->json($e);
        }
    }
    
    public function googleCallback()
    {
        $data = $this->registerOrLogin(Socialite::driver('google')->user());
        $remember_token = $this->storeRememberToken($data);
        return Redirect::away("http://localhost:8080/login?id=" . Crypt::encrypt($remember_token->id));
    }

    public function login(LoginRequest $request)
    {
        if($this->authenticate($request->validated())) {
            return response()->json($this->generateCredentials(auth()->user()->email), 200);
        }
        return response()->json(['message' => 'Invalid credentials.'], 401);
    }

    public function logout(Request $request)
    {
        $accessToken = $request->bearerToken();
        $token = PersonalAccessToken::findToken($accessToken);
        $token->delete();
        return response()->json(['message' => 'Logged out successfully.'], 200);
    }

    public function me()
    {
        return Auth::user();
    }

    public function register(RegistrationRequest $request)
    {
        $user = User::create($request->validated());
        return response()->json([
            'message' => "Your registraion is successful. Please wait for Admin's approval."
        ]);
    }

    private function authenticate($user)
    {
        return Auth::attempt($user);
    }


    private function generateCredentials($email)
    {
        $user = User::where('email', $email)->firstOrFail();
        $access_token = $user->createToken('access-token')->plainTextToken;

        return [
            'user'          => $user,
            'access_token'  => $access_token
        ];
    }

    private function registerOrLogin(ContractsUser $socialUser)
    {
        $user = User::where('email', $socialUser->getEmail())->first();
        if($user) {
            $this->authenticate($this->userCredentials($user));
            return $this->generateCredentials($user->email);
        }
        
        $name = split_name($socialUser->getName());
        $user = User::create([
            'first_name'    => $name['first_name'],
            'last_name'     => $name['last_name'],
            'email'         => $socialUser->getEmail(),
            'password'      => Hash::make(config('global.default_password')),
            'user_type'     => User::INTERN
        ]);
        $this->authenticate($this->userCredentials($user));
        return $this->generateCredentials($user->email);
    }

    private function storeRememberToken($data) 
    {
        
       return RememberToken::create([
            'user_id'       => $data['user']['id'],
            'access_token'  => $data['access_token']
       ]);
    }

    private function userCredentials(User $user)
    {
        return ['email' => $user->email, 'password' => $user->password];
    }
}
