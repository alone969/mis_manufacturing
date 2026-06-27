<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * List all employees (admin/manager).
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::select('id', 'name', 'email', 'role', 'is_email_verified', 'onboarding_status', 'created_at');

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderByDesc('created_at')
            ->paginate($request->get('per_page', 20));

        return response()->json($employees);
    }

    /**
     * Create a new employee (admin).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:employee,manager,admin',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => $request->role,
            'is_email_verified' => false,
            'onboarding_status' => 'active',
        ]);

        $user->assignRole($request->role);

        $this->logActivity('create', "Created user: {$user->name}", User::class, $user->id);

        return response()->json([
            'message' => 'Employee created successfully.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ], 201);
    }

    /**
     * Get employee details.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'is_email_verified' => $user->is_email_verified,
            'onboarding_status' => $user->onboarding_status,
            'settings' => $user->settings,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    }

    /**
     * Update an employee (admin).
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'role' => 'sometimes|in:employee,manager,admin',
        ]);

        $oldRole = $user->role;

        $user->update($request->only(['name', 'email', 'role']));

        // Update Spatie role if role changed
        if ($request->has('role') && $request->role !== $oldRole) {
            $user->syncRoles([$request->role]);
        }

        $this->logActivity('update', "Updated user: {$user->name}", User::class, $user->id);

        return response()->json([
            'message' => 'Employee updated successfully.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    /**
     * Delete an employee (admin).
     */
    public function destroy(User $user): JsonResponse
    {
        $userName = $user->name;
        $user->delete();

        $this->logActivity('delete', "Deleted user: {$userName}", User::class, $user->id);

        return response()->json([
            'message' => 'Employee deleted successfully.',
        ]);
    }

    /**
     * Toggle employee active/deactive status (admin).
     */
    public function toggleStatus(User $user): JsonResponse
    {
        $newStatus = $user->onboarding_status === 'active' ? 'deactivated' : 'active';
        $user->update(['onboarding_status' => $newStatus]);

        $this->logActivity('update', "User {$user->name} status set to {$newStatus}", User::class, $user->id);

        return response()->json([
            'message' => "Employee {$newStatus} successfully.",
            'onboarding_status' => $newStatus,
        ]);
    }
}
