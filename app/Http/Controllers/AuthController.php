<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * إنشاء حساب جديد وتسجيل الدخول مباشرة.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).*$/',
            ],
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number.',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'status' => 'active',
        ]);

        $token = $user->createToken('react-app')->plainTextToken;

        return response()->json([
            'message' => 'Account created successfully.',
            'user' => $this->userData($user),
            'token' => $token,
        ], 201);
    }

    /**
     * تسجيل الدخول.
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::with('role.permissions')
            ->where('email', $data['email'])
            ->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'Your account is inactive.',
            ], 403);
        }

        $token = $user->createToken('react-app')->plainTextToken;

        return response()->json([
            'user' => $this->userData($user),
            'token' => $token,
        ]);
    }

    /**
     * تسجيل الخروج من جميع الجلسات API.
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * البيانات التي يعاد إرسالها للواجهة.
     *
     * نحتفظ بالـ role والـ permissions لتوافق الواجهة الحالية،
     * حتى لو لم نستخدمهما في النموذج الشخصي حالياً.
     */
    private function userData(User $user): array
    {
        $user->loadMissing(['role.permissions']);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'status' => $user->status,

            'role' => $user->role
                ? [
                    'id' => $user->role->id,
                    'name' => $user->role->name,
                ]
                : null,

            'permissions' => $user->role
                ? $user->role->permissions
                    ->pluck('name')
                    ->values()
                    ->toArray()
                : [],
        ];
    }
}

