<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\AuthRepositoryInterface;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthRepository implements AuthRepositoryInterface
{
    // public function register(array $data)
    // {
    //     $user = User::create([
    //         'name' => $data['name'],
    //         'email' => $data['email'],
    //         'password' => Hash::make($data['password']),
    //         'role' => 'user',
    //         'phone' => $data['phone'] ?? null,
    //         'birth_date' => $data['birth_date'] ?? null,
    //     ]);

    //     $token = JWTAuth::fromUser($user);

    //     return compact('user', 'token');
    // }

    public function register(array $data)
{
    $userData = [
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
        'phone' => $data['phone'] ?? null,
        'birth_date' => $data['birth_date'] ?? null,
        'role' => $data['role'] ?? 'user', 
    ];

    $user = User::create($userData);
    $token = JWTAuth::fromUser($user);

    return compact('user', 'token');
}

    public function login(array $credentials)
    {
        if (!$token = JWTAuth::attempt($credentials)) {
            return null;
        }

        return $token;
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return true;
    }

    public function refresh()
    {
        return JWTAuth::refresh(JWTAuth::getToken());
    }

    public function userProfile()
    {
        return JWTAuth::user();
    }

    public function updateUser(array $data)
{
    $user = JWTAuth::user();
    $user->update($data);
    return $user;
}

public function deleteUser()
{
    $user = JWTAuth::user();
    JWTAuth::invalidate(JWTAuth::getToken());
    return $user->delete();
}

public function adminLogin(array $credentials)
{
    if (!$token = auth()->attempt($credentials)) {
        return false;
    }

    $user = auth()->user();
    
    if ($user->email !== 'admin@example.com') { // Vérification stricte par email
        auth()->logout(); // Déconnecte si ce n'est pas le bon email
        return false;
    }

    return $token;
}
}