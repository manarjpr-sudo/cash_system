<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Role;
use App\Mail\UserRegisteredMail; // ✅ إضافة
use Illuminate\Support\Facades\Mail; // ✅ إضافة

class AuthController extends Controller
{
    /**
     * Public registration.
     *
     * The requested role is only a request.
     * The final role is assigned by an administrator after approval.
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
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).*$/'
            ],
            'requested_role_id' => [
                'required',
                'exists:roles,id',
                function ($attribute, $value, $fail) {
                    $role = Role::find($value);
                    if (!$role || $role->name === 'admin') {
                        $fail('This role cannot be requested.');
                    }
                },
            ],
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number.'
        ]);

        $requestedRole = Role::findOrFail($data['requested_role_id']);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => null,
            'requested_role_id' => $requestedRole->id,
            'status' => 'pending',
        ]);

        // ✅ إرسال إشعار للمدير
        $admin = User::whereHas('role', function ($q) {
            $q->where('name', 'Admin');
        })->first();

        if ($admin) {
            Mail::to($admin->email)->send(new UserRegisteredMail($user));
        }

        return response()->json([
            'message' => 'Registration submitted and is awaiting administrator approval.',
            'user' => $this->userData($user),
        ], 201);
    }

    /**
     * Public endpoint used by the React registration page.
     *
     * Admin is intentionally excluded from public registration.
     */
    public function registrationRoles()
    {
        return response()->json(
            Role::where('name', '!=', 'admin')
                ->orderBy('id')
                ->get(['id', 'name', 'description'])
        );
    }

    /**
     * Login.
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
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        if ($user->status === 'pending') {
            return response()->json(['message' => 'Your registration is still awaiting administrator approval.'], 403);
        }

        if ($user->status === 'rejected') {
            return response()->json([
                'message' => 'Your registration request was rejected.',
                'rejection_reason' => $user->rejection_reason,
            ], 403);
        }

        if ($user->status === 'inactive') {
            return response()->json(['message' => 'Your account is inactive.'], 403);
        }

        if ($user->status !== 'active') {
            return response()->json(['message' => 'Your account is not available for login.'], 403);
        }

        $token = $user->createToken('react-app')->plainTextToken;

        return response()->json([
            'user' => $this->userData($user),
            'token' => $token,
        ]);
    }

    /**
     * Logout from all active API tokens.
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out.']);
    }

    /**
     * Normalize user data returned to React.
     */
    private function userData(User $user): array
    {
        $user->loadMissing(['role.permissions', 'requestedRole']);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'status' => $user->status,
            'role' => $user->role ? ['id' => $user->role->id, 'name' => $user->role->name] : null,
            'requested_role' => $user->requestedRole ? ['id' => $user->requestedRole->id, 'name' => $user->requestedRole->name] : null,
            'permissions' => $user->role ? $user->role->permissions->pluck('name')->values()->toArray() : [],
            'approved_at' => $user->approved_at,
            'rejected_at' => $user->rejected_at,
            'rejection_reason' => $user->rejection_reason,
        ];
    }
}