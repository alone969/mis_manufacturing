<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebEmployeeController extends Controller
{
    /**
     * List employees (admin/manager).
     */
    public function index(Request $request): View
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

        $employees = $query->orderByDesc('created_at')->paginate(15);

        return view('employees.index', compact('employees'));
    }

    /**
     * Show create employee form (admin).
     */
    public function create(): View
    {
        return view('employees.create');
    }

    /**
     * Store employee (admin).
     */
    public function store(Request $request)
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
        $this->logActivity('create', "Created employee: {$user->name}", User::class, $user->id);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    /**
     * Show employee details.
     */
    public function show(User $user): View
    {
        $user->load(['shiftAssignments.shift:name,start_time,end_time', 'salaries']);
        return view('employees.show', compact('user'));
    }

    /**
     * Show edit employee form (admin).
     */
    public function edit(User $user): View
    {
        return view('employees.edit', compact('user'));
    }

    /**
     * Update employee (admin).
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'role' => 'sometimes|in:employee,manager,admin',
        ]);

        $oldRole = $user->role;
        $user->update($request->only(['name', 'email', 'role']));

        if ($request->has('role') && $request->role !== $oldRole) {
            $user->syncRoles([$request->role]);
        }

        $this->logActivity('update', "Updated employee: {$user->name}", User::class, $user->id);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    /**
     * Delete employee (admin).
     */
    public function destroy(User $user)
    {
        $userName = $user->name;
        $user->delete();
        $this->logActivity('delete', "Deleted employee: {$userName}", User::class, $user->id);

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }

    /**
     * Toggle employee status (admin).
     */
    public function toggleStatus(User $user)
    {
        $newStatus = $user->onboarding_status === 'active' ? 'deactivated' : 'active';
        $user->update(['onboarding_status' => $newStatus]);

        $this->logActivity('update', "Employee {$user->name} status: {$newStatus}", User::class, $user->id);

        return redirect()->route('employees.index')->with('success', "Employee {$newStatus} successfully.");
    }
}
