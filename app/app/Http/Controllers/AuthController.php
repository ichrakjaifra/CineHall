<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Contracts\AuthRepositoryInterface;

class AuthController extends Controller
{
    private $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(Request $request)
    {
      $request->validate([
        'name' => 'required|string',
        'email' => 'required|string|email|unique:users',
        'password' => 'required|string|min:6',
        'phone' => 'nullable|string',
        'birth_date' => 'nullable|date',
        'role' => 'nullable|string|in:user,admin', 
    ]);

    // Protection contre l'auto-attribution du rôle admin
    $data = $request->all();
    if (!auth()->check() || auth()->user()->role !== 'admin') {
        $data['role'] = 'user'; // Force le rôle user si pas admin
    }

        $result = $this->authRepository->register($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $result['user'],
            'authorisation' => [
                'token' => $result['token'],
                'type' => 'bearer',
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $token = $this->authRepository->login($request->only('email', 'password'));

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = $this->authRepository->userProfile();

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

//     public function login(Request $request)
// {
//     $request->validate([
//         'email' => 'required|string|email',
//         'password' => 'required|string',
//     ]);

//     $credentials = $request->only('email', 'password');

//     if (!$token = auth()->attempt($credentials)) {
//         return response()->json(['message' => 'Identifiants incorrects'], 401);
//     }

//     $user = auth()->user();
    
//     return response()->json([
//         'user' => $user,
//         'token' => $token,
//         'is_admin' => $user->email === 'admin@example.com' 
//     ]);
// }
    

    public function logout()
    {
        $this->authRepository->logout();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => $this->authRepository->userProfile(),
            'authorisation' => [
                'token' => $this->authRepository->refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

    public function userProfile()
    {
        return response()->json([
            'status' => 'success',
            'user' => $this->authRepository->userProfile(),
        ]);
    }

    
    public function updateProfile(Request $request)
{
    $request->validate([
        'name' => 'sometimes|string|max:255',
        'email' => 'sometimes|string|email|max:255|unique:users,email,'.auth()->id(),
        'password' => 'sometimes|string|min:6|confirmed',
        'phone' => 'nullable|string|max:20',
        'birth_date' => 'nullable|date',
    ]);

    $data = $request->all();
    
    if ($request->has('password')) {
        $data['password'] = Hash::make($request->password);
    }

    $user = $this->authRepository->updateUser($data);

    return response()->json([
        'status' => 'success',
        'message' => 'Profile updated successfully',
        'user' => $user
    ]);
}

public function deleteAccount()
{
    $this->authRepository->deleteUser();

    return response()->json([
        'status' => 'success',
        'message' => 'Account deleted successfully'
    ]);
}

}