<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required'      => 'Imię jest wymagane.',
            'name.max'           => 'Imię może mieć maksymalnie 255 znaków.',
            'email.required'     => 'Adres e-mail jest wymagany.',
            'email.email'        => 'Podaj prawidłowy adres e-mail.',
            'email.unique'       => 'Ten adres e-mail jest już zajęty.',
            'password.required'  => 'Hasło jest wymagane.',
            'password.min'       => 'Hasło musi mieć co najmniej 8 znaków.',
            'password.confirmed' => 'Hasła nie są identyczne.',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('api', ['*'], now()->addHours(1))->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Adres e-mail jest wymagany.',
            'email.email'       => 'Podaj prawidłowy adres e-mail.',
            'password.required' => 'Hasło jest wymagane.',
        ]);

        if (!Auth::attempt($data)) {
            throw ValidationException::withMessages([
                'email' => ['Nieprawidłowe dane logowania.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('api', ['*'], now()->addHours(1))->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Wylogowano.']);
    }
}
