<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use App\Http\Requests\ApproveUserRequest;
use App\Http\Requests\RejectUserRequest;
use Illuminate\Http\Request;
use App\Services\AuditLogService;
use App\Mail\UserApprovedMail;   // ✅ إضافة
use App\Mail\UserRejectedMail;   // ✅ إضافة
use Illuminate\Support\Facades\Mail; // ✅ إضافة

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $users = User::with(['role', 'requestedRole', 'approver', 'rejector'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->role_id, fn($q, $v) => $q->where('role_id', $v))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return response()->json($users);
    }

    public function roles()
    {
        return response()->json(
            Role::orderBy('name')->get(['id', 'name', 'description'])
        );
    }

    public function approve(ApproveUserRequest $request, User $user)
    {
        try {
            $oldStatus = $user->status;
            $oldRole = $user->role_id;

            $updatedUser = $this->userService->approve($user, $request->validated(), $request->user()->id);

            // تسجيل في سجل التدقيق
            AuditLogService::log(
                'approve_user',
                'user',
                $user->id,
                ['status' => $oldStatus, 'role_id' => $oldRole],
                ['status' => 'active', 'role_id' => $request->validated()['role_id']],
                "User {$user->name} approved by " . $request->user()->name
            );

            // ✅ إرسال إشعار للمستخدم
            Mail::to($user->email)->send(new UserApprovedMail($user));

            return response()->json(['message' => 'User approved successfully.', 'user' => $updatedUser]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function reject(RejectUserRequest $request, User $user)
    {
        try {
            $oldStatus = $user->status;

            $updatedUser = $this->userService->reject($user, $request->validated()['rejection_reason'], $request->user()->id);

            // تسجيل في سجل التدقيق
            AuditLogService::log(
                'reject_user',
                'user',
                $user->id,
                ['status' => $oldStatus],
                ['status' => 'rejected', 'rejection_reason' => $request->validated()['rejection_reason']],
                "User {$user->name} rejected by " . $request->user()->name
            );

            // ✅ إرسال إشعار للمستخدم
            Mail::to($user->email)->send(new UserRejectedMail($user));

            return response()->json(['message' => 'User registration rejected.', 'user' => $updatedUser]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function deactivate(Request $request, User $user)
    {
        try {
            $oldStatus = $user->status;

            $this->userService->deactivate($user, $request->user()->id);

            AuditLogService::log(
                'deactivate_user',
                'user',
                $user->id,
                ['status' => $oldStatus],
                ['status' => 'inactive'],
                "User {$user->name} deactivated by " . $request->user()->name
            );

            return response()->json(['message' => 'User deactivated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function activate(User $user)
    {
        try {
            $oldStatus = $user->status;

            $this->userService->activate($user);

            AuditLogService::log(
                'activate_user',
                'user',
                $user->id,
                ['status' => $oldStatus],
                ['status' => 'active'],
                "User {$user->name} activated by " . auth()->user()->name
            );

            return response()->json(['message' => 'User activated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function show(User $user)
    {
        return response()->json(
            $user->load(['role.permissions', 'requestedRole', 'approver', 'rejector'])
        );
    }
}