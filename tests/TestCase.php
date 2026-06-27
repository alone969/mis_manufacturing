<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions after migrations have run
        $this->seedRolesAndPermissions();
    }

    /**
     * Seed roles and permissions for testing.
     */
    protected function seedRolesAndPermissions(): void
    {
        // Reset cached roles and permissions
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'manage-employees', 'manage-shifts', 'manage-stock', 'process-salaries',
            'view-attendance', 'view-activity-logs', 'view-device-logs', 'manage-notifications',
            'send-messages', 'view-profile', 'edit-profile', 'clock-in-out', 'view-salary', 'view-stock',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Create roles with permissions
        $employee = Role::findOrCreate('employee');
        $employee->syncPermissions(['view-profile', 'edit-profile', 'clock-in-out', 'view-salary', 'view-stock', 'send-messages']);

        $manager = Role::findOrCreate('manager');
        $manager->syncPermissions(['view-profile', 'edit-profile', 'clock-in-out', 'view-salary', 'view-stock', 'send-messages', 'manage-shifts', 'view-attendance']);

        $admin = Role::findOrCreate('admin');
        $admin->syncPermissions($permissions);
    }

    /**
     * Helper to create an employee user for testing.
     */
    protected function createEmployee($overrides = []): \App\Models\User
    {
        $user = \App\Models\User::factory()->create(array_merge([
            'role' => 'employee',
            'is_email_verified' => true,
            'onboarding_status' => 'active',
        ], $overrides));
        $user->assignRole('employee');
        return $user;
    }

    /**
     * Helper to create a manager user for testing.
     */
    protected function createManager($overrides = []): \App\Models\User
    {
        $user = \App\Models\User::factory()->create(array_merge([
            'role' => 'manager',
            'is_email_verified' => true,
            'onboarding_status' => 'active',
        ], $overrides));
        $user->assignRole('manager');
        return $user;
    }

    /**
     * Helper to create an admin user for testing.
     */
    protected function createAdmin($overrides = []): \App\Models\User
    {
        $user = \App\Models\User::factory()->create(array_merge([
            'role' => 'admin',
            'is_email_verified' => true,
            'onboarding_status' => 'active',
        ], $overrides));
        $user->assignRole('admin');
        return $user;
    }
}
