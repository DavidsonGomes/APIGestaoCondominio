<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Unit;

/**
 * Class AuthController
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function unauthorized()
    {
        return response()->json([
            "error" => 'Não autorizado'
        ], 401);
    }

    /**
     * @param Request $request
     * @return string[]
     */
    public function register(Request $request)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|digits:11|unique:users,cpf',
            'password' => 'required',
            'password_confirm' => 'required|same:password'
        ]);

        if($validator->fails()){
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        $name = $request->input('name');
        $email = $request->input('email');
        $cpf = $request->input('cpf');
        $password = $request->input('password');

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $newUser = new User();
        $newUser->name = $name;
        $newUser->email = $email;
        $newUser->cpf = $cpf;
        $newUser->password = $hash;

        $newUser->save();

        $token = auth()->attempt([
            'cpf' => $cpf,
            'password' => $password
        ]);

        if(!$token){
            $array['error'] = 'Ocorreu um erro.';
            return $array;
        }

        $array['token'] = $token;

        $user = auth()->user();
        $array['user'] = $user;

        $proprieties = Unit::select(['id', 'name'])
            ->where('id_owner', $user['id'])
            ->get();

        $array['user']['proprieties'] = $proprieties;

        return $array;

    }

    /**
     * @param Request $request
     * @return string[]
     */
    public function login(Request $request)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'cpf' => 'required|digits:11',
            'password' => 'required',
        ]);

        if($validator->fails()){
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        $cpf = $request->input('cpf');
        $password = $request->input('password');

        $token = auth()->attempt([
            'cpf' => $cpf,
            'password' => $password
        ]);

        if(!$token){
            $array['error'] = 'CPF e/ou Senha estão incorretos.';
            return $array;
        }

        $array['token'] = $token;

        $user = auth()->user();
        $array['user'] = $user;

        $proprieties = Unit::select(['id', 'name'])
            ->where('id_owner', $user['id'])
            ->get();

        $array['user']['proprieties'] = $proprieties;

        return $array;
    }

    /**
     * @return string[]
     */
    public function validateToken()
    {
        $array = ['error' => ''];

        $user = auth()->user();
        $array['user'] = $user;

        $proprieties = Unit::select(['id', 'name'])
            ->where('id_owner', $user['id'])
            ->get();

        $array['user']['proprieties'] = $proprieties;

        return $array;
    }

    /**
     * @return string[]
     */
    public function logout()
    {
        $array = ['error' => ''];

        auth()->logout();

        return $array;
    }
}
