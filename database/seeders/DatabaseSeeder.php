<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Shift;
use App\Models\ShiftAssignment;
use App\Models\StockItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'manage-employees', 'manage-shifts', 'manage-stock', 'process-salaries',
            'view-attendance', 'view-activity-logs', 'view-device-logs', 'manage-notifications',
            'send-messages', 'view-profile', 'edit-profile', 'clock-in-out', 'view-salary', 'view-stock',
        ];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles
        $employeeRole = Role::create(['name' => 'employee']);
        $employeeRole->givePermissionTo(['view-profile', 'edit-profile', 'clock-in-out', 'view-salary', 'view-stock', 'send-messages']);

        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo(['view-profile', 'edit-profile', 'clock-in-out', 'view-salary', 'view-stock', 'send-messages', 'manage-shifts', 'view-attendance']);

        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo($permissions);

        // ===== ADMIN =====
        $adminUser = User::create([
            'name' => 'Rajesh Shrestha',
            'email' => 'admin@mis-manufacturing.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_email_verified' => true,
            'onboarding_status' => 'active',
        ]);
        $adminUser->assignRole('admin');

        // ===== MANAGERS =====
        $manager1 = $this->createUser('Sita Sharma', 'manager1@mis-manufacturing.com', 'manager');
        $manager2 = $this->createUser('Binod Thapa', 'manager2@mis-manufacturing.com', 'manager');

        // ===== EMPLOYEES =====
        $employees = [];
        $employeeData = [
            ['name' => 'Ram Bahadur Gurung', 'email' => 'ram@mis-manufacturing.com'],
            ['name' => 'Sunita Magar', 'email' => 'sunita@mis-manufacturing.com'],
            ['name' => 'Krishna Prasad Adhikari', 'email' => 'krishna@mis-manufacturing.com'],
            ['name' => 'Laxmi Devi Poudel', 'email' => 'laxmi@mis-manufacturing.com'],
            ['name' => 'Hari Krishna Karki', 'email' => 'hari@mis-manufacturing.com'],
            ['name' => 'Gita Kumari Bhandari', 'email' => 'gita@mis-manufacturing.com'],
            ['name' => 'Prakash Rai', 'email' => 'prakash@mis-manufacturing.com'],
            ['name' => 'Anita Thakuri', 'email' => 'anita@mis-manufacturing.com'],
            ['name' => 'Deepak Limbu', 'email' => 'deepak@mis-manufacturing.com'],
            ['name' => 'Sarita Tamang', 'email' => 'sarita@mis-manufacturing.com'],
            ['name' => 'Rajan Chhetri', 'email' => 'rajan@mis-manufacturing.com'],
            ['name' => 'Mina Khatiwada', 'email' => 'mina@mis-manufacturing.com'],
        ];

        foreach ($employeeData as $emp) {
            $employees[] = $this->createUser($emp['name'], $emp['email'], 'employee');
        }

        // ===== SHIFTS =====
        $morningShift = Shift::create(['name' => 'Morning Shift', 'start_time' => '06:00', 'end_time' => '14:00', 'created_by' => $adminUser->id]);
        $afternoonShift = Shift::create(['name' => 'Afternoon Shift', 'start_time' => '14:00', 'end_time' => '22:00', 'created_by' => $adminUser->id]);
        $nightShift = Shift::create(['name' => 'Night Shift', 'start_time' => '22:00', 'end_time' => '06:00', 'created_by' => $adminUser->id]);
        $fullDayShift = Shift::create(['name' => 'Full Day', 'start_time' => '08:00', 'end_time' => '17:00', 'created_by' => $adminUser->id]);

        $shifts = [$morningShift, $afternoonShift, $nightShift, $fullDayShift];

        // ===== SHIFT ASSIGNMENTS (past 7 days + today + next 3 days) =====
        $statuses = ['present', 'present', 'present', 'assigned', 'absent', 'late', 'assigned'];
        $allUsers = array_merge([$manager1, $manager2], $employees);

        for ($day = -7; $day <= 3; $day++) {
            $date = now()->addDays($day)->toDateString();

            foreach ($allUsers as $i => $user) {
                if (($i + abs($day)) % 4 === 0) continue;

                $shift = $shifts[$i % count($shifts)];

                $data = [
                    'user_id' => $user->id,
                    'shift_id' => $shift->id,
                    'date' => $date,
                    'status' => 'assigned',
                ];

                if ($day < 0) {
                    $status = $statuses[array_rand($statuses)];
                    $data['status'] = $status;

                    if ($status === 'present' || $status === 'late') {
                        $clockInHour = $status === 'late' ? (int) substr($shift->start_time, 0, 2) + rand(1, 2) : (int) substr($shift->start_time, 0, 2);
                        $clockInMin = $status === 'late' ? rand(5, 59) : rand(0, 10);
                        $data['clock_in'] = $date . ' ' . str_pad($clockInHour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($clockInMin, 2, '0', STR_PAD_LEFT) . ':00';

                        $endHour = (int) substr($shift->end_time, 0, 2);
                        $data['clock_out'] = $date . ' ' . str_pad($endHour, 2, '0', STR_PAD_LEFT) . ':' . str_pad(rand(0, 15), 2, '0', STR_PAD_LEFT) . ':00';
                    }
                }

                ShiftAssignment::create($data);
            }
        }

        // ===== STOCK ITEMS (matching actual schema columns) =====
        $stockItems = [
            ['name' => 'Cotton Fabric (White)', 'type' => 'raw_material', 'quantity' => 800, 'unit' => 'meters', 'minimum_quantity' => 200, 'sku' => 'RM-COT-WHT'],
            ['name' => 'Cotton Fabric (Black)', 'type' => 'raw_material', 'quantity' => 600, 'unit' => 'meters', 'minimum_quantity' => 150, 'sku' => 'RM-COT-BLK'],
            ['name' => 'Silk Fabric', 'type' => 'raw_material', 'quantity' => 150, 'unit' => 'meters', 'minimum_quantity' => 50, 'sku' => 'RM-SLK'],
            ['name' => 'Denim Fabric', 'type' => 'raw_material', 'quantity' => 400, 'unit' => 'meters', 'minimum_quantity' => 100, 'sku' => 'RM-DNM'],
            ['name' => 'Plastic Buttons (Small)', 'type' => 'raw_material', 'quantity' => 8000, 'unit' => 'pieces', 'minimum_quantity' => 2000, 'sku' => 'RM-BTN-SM'],
            ['name' => 'Metal Buttons (Large)', 'type' => 'raw_material', 'quantity' => 3000, 'unit' => 'pieces', 'minimum_quantity' => 1000, 'sku' => 'RM-BTN-LG'],
            ['name' => 'Thread (White)', 'type' => 'raw_material', 'quantity' => 120, 'unit' => 'spools', 'minimum_quantity' => 30, 'sku' => 'RM-THR-WHT'],
            ['name' => 'Thread (Black)', 'type' => 'raw_material', 'quantity' => 85, 'unit' => 'spools', 'minimum_quantity' => 30, 'sku' => 'RM-THR-BLK'],
            ['name' => 'Thread (Red)', 'type' => 'raw_material', 'quantity' => 8, 'unit' => 'spools', 'minimum_quantity' => 20, 'sku' => 'RM-THR-RED'],
            ['name' => 'Elastic Band', 'type' => 'raw_material', 'quantity' => 200, 'unit' => 'meters', 'minimum_quantity' => 50, 'sku' => 'RM-ELA'],
            ['name' => 'T-Shirt (S) - White', 'type' => 'finished_good', 'quantity' => 200, 'unit' => 'pieces', 'minimum_quantity' => 50, 'sku' => 'FG-TSH-S'],
            ['name' => 'T-Shirt (M) - White', 'type' => 'finished_good', 'quantity' => 350, 'unit' => 'pieces', 'minimum_quantity' => 50, 'sku' => 'FG-TSH-M'],
            ['name' => 'T-Shirt (L) - White', 'type' => 'finished_good', 'quantity' => 280, 'unit' => 'pieces', 'minimum_quantity' => 50, 'sku' => 'FG-TSH-L'],
            ['name' => 'Jeans (30)', 'type' => 'finished_good', 'quantity' => 150, 'unit' => 'pieces', 'minimum_quantity' => 30, 'sku' => 'FG-JNS-30'],
            ['name' => 'Jeans (32)', 'type' => 'finished_good', 'quantity' => 180, 'unit' => 'pieces', 'minimum_quantity' => 30, 'sku' => 'FG-JNS-32'],
            ['name' => 'Jeans (34)', 'type' => 'finished_good', 'quantity' => 120, 'unit' => 'pieces', 'minimum_quantity' => 30, 'sku' => 'FG-JNS-34'],
            ['name' => 'Kurta (M)', 'type' => 'finished_good', 'quantity' => 6, 'unit' => 'pieces', 'minimum_quantity' => 20, 'sku' => 'FG-KRT-M'],
            ['name' => 'Kurta (L)', 'type' => 'finished_good', 'quantity' => 45, 'unit' => 'pieces', 'minimum_quantity' => 20, 'sku' => 'FG-KRT-L'],
        ];

        foreach ($stockItems as $item) {
            StockItem::create(array_merge($item, [
                'updated_by' => $adminUser->id,
            ]));
        }

        // ===== SALARY RECORDS (matching actual schema: user_id, amount, period_start, period_end, status, paid_at, processed_by, notes) =====
        foreach ($allUsers as $user) {
            $amount = 15000 + ($user->role === 'manager' ? 20000 : 0);

            \App\Models\Salary::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'period_start' => now()->subMonth()->startOfMonth()->toDateString(),
                'period_end' => now()->subMonth()->endOfMonth()->toDateString(),
                'status' => 'paid',
                'paid_at' => now()->subDays(5),
                'processed_by' => $adminUser->id,
                'notes' => 'Monthly salary for ' . now()->subMonth()->format('F Y'),
            ]);

            \App\Models\Salary::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'period_start' => now()->startOfMonth()->toDateString(),
                'period_end' => now()->endOfMonth()->toDateString(),
                'status' => 'pending',
                'processed_by' => $adminUser->id,
                'notes' => 'Monthly salary for ' . now()->format('F Y'),
            ]);
        }

        // ===== MESSAGES =====
        \App\Models\Message::create(['sender_id' => $adminUser->id, 'receiver_id' => $manager1->id, 'subject' => 'Welcome to the Team', 'body' => 'Dear Sita, welcome to MIS Manufacturing. Please review the shift schedules for next week.']);
        \App\Models\Message::create(['sender_id' => $manager1->id, 'receiver_id' => $employees[0]->id, 'subject' => 'Shift Update', 'body' => 'Hi Ram, you have been assigned to the morning shift starting tomorrow.']);
        \App\Models\Message::create(['sender_id' => $adminUser->id, 'receiver_id' => $manager2->id, 'subject' => 'Stock Alert', 'body' => 'Binod, please check the low stock on Thread (Red) and place an order.']);

        // ===== NOTIFICATIONS =====
        foreach (array_slice($allUsers, 0, 8) as $user) {
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'type' => 'info',
                'title' => 'Shift Assigned',
                'message' => 'Your shift has been updated for this week.',
                'is_read' => rand(0, 1),
            ]);
        }

        \App\Models\Notification::create([
            'user_id' => $adminUser->id,
            'type' => 'warning',
            'title' => 'Low Stock Alert',
            'message' => 'Thread (Red) is running low. Current stock: 8 spools.',
            'is_read' => false,
        ]);

        \App\Models\Notification::create([
            'user_id' => $adminUser->id,
            'type' => 'success',
            'title' => 'Salary Processed',
            'message' => 'Monthly salaries for ' . now()->subMonth()->format('F Y') . ' have been processed.',
            'is_read' => true,
        ]);

        // ===== ACTIVITY LOGS =====
        ActivityLog::log($adminUser, 'login', 'Admin logged in');
        ActivityLog::log($manager1, 'clock_in', 'Manager clocked in for morning shift');
        ActivityLog::log($employees[0], 'clock_in', 'Employee clocked in');

        echo "\n========================================\n";
        echo " Database seeded successfully!\n";
        echo "========================================\n";
        echo " Admin:     admin@mis-manufacturing.com / password\n";
        echo " Manager 1: manager1@mis-manufacturing.com / password\n";
        echo " Manager 2: manager2@mis-manufacturing.com / password\n";
        echo " Employee:  ram@mis-manufacturing.com / password\n";
        echo "========================================\n";
    }

    private function createUser(string $name, string $email, string $role): User
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => $role,
            'is_email_verified' => true,
            'onboarding_status' => 'active',
        ]);
        $user->assignRole($role);
        return $user;
    }
}
