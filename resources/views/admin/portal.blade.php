<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Portal - MIS Manufacturing</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; background: #f1f5f9; color: #1e293b; line-height: 1.6; }
        a { text-decoration: none; color: inherit; }

        /* Sidebar */
        .admin-layout { display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%); color: white; position: fixed; top: 0; left: 0; bottom: 0; z-index: 50; display: flex; flex-direction: column; }
        .sidebar-header { padding: 20px 24px; border-bottom: 1px solid rgba(255,255,255,0.08); display: flex; align-items: center; gap: 12px; }
        .sidebar-logo { width: 40px; height: 40px; background: linear-gradient(135deg, #2563eb, #1d4ed8); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px; color: white; }
        .sidebar-brand { font-size: 15px; font-weight: 700; }
        .sidebar-brand small { display: block; font-size: 10px; font-weight: 500; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; }
        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .sidebar-section { font-size: 10px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 1px; padding: 12px 12px 6px; }
        .sidebar-link { display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 8px; font-size: 14px; font-weight: 500; color: #94a3b8; transition: all 0.2s; margin-bottom: 2px; }
        .sidebar-link:hover { background: rgba(255,255,255,0.06); color: white; }
        .sidebar-link.active { background: rgba(37,99,235,0.2); color: #60a5fa; }
        .sidebar-link .icon { width: 20px; text-align: center; font-size: 16px; }
        .sidebar-link .badge { margin-left: auto; background: #ef4444; color: white; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 10px; }
        .sidebar-footer { padding: 16px 20px; border-top: 1px solid rgba(255,255,255,0.08); }
        .sidebar-user { display: flex; align-items: center; gap: 10px; }
        .sidebar-avatar { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #2563eb, #7c3aed); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; }
        .sidebar-user-info { flex: 1; }
        .sidebar-user-name { font-size: 13px; font-weight: 600; }
        .sidebar-user-role { font-size: 11px; color: #64748b; }

        /* Main Content */
        .main-content { margin-left: 260px; flex: 1; }
        .topbar { background: white; border-bottom: 1px solid #e2e8f0; padding: 0 32px; height: 64px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 40; }
        .topbar-title { font-size: 18px; font-weight: 700; color: #0f172a; }
        .topbar-actions { display: flex; align-items: center; gap: 12px; }
        .topbar-date { font-size: 13px; color: #64748b; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.2s; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-success { background: #16a34a; color: white; }
        .btn-success:hover { background: #15803d; }
        .btn-danger { background: #dc2626; color: white; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-outline { background: white; color: #475569; border: 1px solid #e2e8f0; }
        .btn-outline:hover { background: #f8fafc; border-color: #cbd5e1; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }

        .content { padding: 32px; }
        .page-header { margin-bottom: 32px; }
        .page-header h1 { font-size: 28px; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
        .page-header p { font-size: 14px; color: #64748b; }

        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 32px; }
        .stat-card { background: white; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0; transition: all 0.3s; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
        .stat-card .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 16px; }
        .stat-card .stat-value { font-size: 28px; font-weight: 800; color: #0f172a; }
        .stat-card .stat-label { font-size: 13px; color: #64748b; margin-top: 2px; }
        .stat-card .stat-change { font-size: 12px; font-weight: 600; margin-top: 8px; }
        .stat-change.up { color: #16a34a; }
        .stat-change.down { color: #dc2626; }

        /* Cards */
        .card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; }
        .card-header { padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
        .card-header h3 { font-size: 16px; font-weight: 700; color: #0f172a; }
        .card-body { padding: 24px; }
        .card-body.no-pad { padding: 0; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px 16px; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
        td { padding: 12px 16px; font-size: 13px; border-bottom: 1px solid #f1f5f9; }
        tr:hover td { background: #f8fafc; }
        .badge { display: inline-flex; padding: 3px 10px; border-radius: 100px; font-size: 11px; font-weight: 600; }
        .badge-green { background: #dcfce7; color: #16a34a; }
        .badge-blue { background: #dbeafe; color: #2563eb; }
        .badge-yellow { background: #fef3c7; color: #d97706; }
        .badge-red { background: #fee2e2; color: #dc2626; }
        .badge-gray { background: #f1f5f9; color: #64748b; }
        .badge-purple { background: #f3e8ff; color: #9333ea; }

        /* Grid Layouts */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px; margin-bottom: 32px; }

        /* Quick Actions Grid */
        .actions-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        .action-card { display: flex; flex-direction: column; align-items: center; gap: 10px; padding: 20px; border-radius: 12px; border: 2px solid #e2e8f0; transition: all 0.2s; cursor: pointer; text-align: center; }
        .action-card:hover { border-color: #2563eb; background: #eff6ff; transform: translateY(-2px); }
        .action-card .action-icon { font-size: 28px; }
        .action-card .action-label { font-size: 13px; font-weight: 600; color: #334155; }

        /* Activity Feed */
        .activity-item { display: flex; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
        .activity-item:last-child { border-bottom: none; }
        .activity-dot { width: 8px; height: 8px; border-radius: 50%; margin-top: 6px; flex-shrink: 0; }
        .activity-text { font-size: 13px; color: #334155; }
        .activity-time { font-size: 11px; color: #94a3b8; margin-top: 2px; }

        /* Shift Modal */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; }
        .modal-overlay.show { display: flex; }
        .modal { background: white; border-radius: 16px; width: 100%; max-width: 520px; box-shadow: 0 25px 50px rgba(0,0,0,0.2); }
        .modal-header { padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .modal-header h3 { font-size: 18px; font-weight: 700; }
        .modal-close { width: 32px; height: 32px; border-radius: 8px; border: none; background: #f1f5f9; cursor: pointer; font-size: 18px; display: flex; align-items: center; justify-content: center; }
        .modal-close:hover { background: #e2e8f0; }
        .modal-body { padding: 24px; }
        .modal-footer { padding: 16px 24px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 8px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; font-family: inherit; transition: border-color 0.2s; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

        /* Toast */
        .toast { position: fixed; top: 20px; right: 20px; background: #0f172a; color: white; padding: 12px 20px; border-radius: 10px; font-size: 14px; font-weight: 500; z-index: 200; opacity: 0; transform: translateY(-10px); transition: all 0.3s; }
        .toast.show { opacity: 1; transform: translateY(0); }
        .toast.success { background: #16a34a; }
        .toast.error { background: #dc2626; }

        @media (max-width: 1024px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
            .actions-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo"><svg viewBox="0 0 100 100" width="22" height="22" xmlns="http://www.w3.org/2000/svg"><path d="M25 15 L35 10 L42 20 L50 17 L58 20 L65 10 L75 15 L85 30 L72 38 L72 82 L28 82 L28 38 L15 30 Z" fill="white" stroke="white" stroke-width="2" stroke-linejoin="round"/><line x1="50" y1="35" x2="50" y2="70" stroke="rgba(255,255,255,0.6)" stroke-width="1.5" stroke-dasharray="4,3"/><circle cx="50" cy="33" r="2.5" fill="white"/></svg></div>
                <div class="sidebar-brand">
                    Admin Portal
                    <small>MIS Manufacturing</small>
                </div>
            </div>
            <nav class="sidebar-nav">
                <div class="sidebar-section">Main</div>
                <a href="{{ route('dashboard') }}" class="sidebar-link"><span class="icon">📊</span> Dashboard</a>
                <a href="{{ url('/admin') }}" class="sidebar-link active"><span class="icon">🛡️</span> Admin Portal</a>

                <div class="sidebar-section">Management</div>
                <a href="{{ route('employees.index') }}" class="sidebar-link"><span class="icon">👥</span> Employees<span class="badge">{{ \App\Models\User::where('role','employee')->count() }}</span></a>
                <a href="{{ route('shifts.index') }}" class="sidebar-link"><span class="icon">⏰</span> Shifts</a>
                <a href="{{ route('shifts.schedule') }}" class="sidebar-link"><span class="icon">📅</span> Schedule</a>
                <a href="{{ route('stock.index') }}" class="sidebar-link"><span class="icon">📦</span> Stock</a>
                <a href="{{ route('salaries.index') }}" class="sidebar-link"><span class="icon">💰</span> Salaries</a>

                <div class="sidebar-section">Communication</div>
                <a href="{{ route('messages.index') }}" class="sidebar-link"><span class="icon">💬</span> Messages</a>
                <a href="{{ route('notifications.index') }}" class="sidebar-link"><span class="icon">🔔</span> Notifications</a>

                <div class="sidebar-section">System</div>
                <a href="{{ route('activity-logs.index') }}" class="sidebar-link"><span class="icon">📊</span> Activity Logs</a>
                <a href="{{ route('device-logs.index') }}" class="sidebar-link"><span class="icon">💻</span> Device Logs</a>
                <a href="{{ route('settings.edit') }}" class="sidebar-link"><span class="icon">⚙️</span> Settings</a>
            </nav>
            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <div class="sidebar-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                    <div class="sidebar-user-info">
                        <div class="sidebar-user-name">{{ Auth::user()->name }}</div>
                        <div class="sidebar-user-role">Administrator</div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <div class="topbar">
                <div class="topbar-title">Admin Portal</div>
                <div class="topbar-actions">
                    <span class="topbar-date">{{ now()->format('l, F j, Y') }}</span>
                    <a href="{{ url('/dashboard') }}" class="btn btn-outline btn-sm">← Back to Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline">
                        @csrf
                        <button type="submit" class="btn btn-outline btn-sm">Logout</button>
                    </form>
                </div>
            </div>

            <div class="content">
                <!-- Page Header -->
                <div class="page-header">
                    <h1>🛡️ Admin Control Panel</h1>
                    <p>Manage your manufacturing facility — employees, shifts, stock, salaries, and system settings.</p>
                </div>

                <!-- Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #eff6ff;">👥</div>
                        <div class="stat-value">{{ $stats['total_employees'] ?? 0 }}</div>
                        <div class="stat-label">Employees</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #f0fdf4;">📋</div>
                        <div class="stat-value">{{ $stats['total_managers'] ?? 0 }}</div>
                        <div class="stat-label">Managers</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #fef3c7;">⏰</div>
                        <div class="stat-value">{{ $stats['active_shifts_today'] ?? 0 }}</div>
                        <div class="stat-label">Active Shifts Today</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #fce7f3;">📦</div>
                        <div class="stat-value">{{ number_format($stats['total_stock_items'] ?? 0) }}</div>
                        <div class="stat-label">Total Stock Units</div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card" style="margin-bottom: 32px;">
                    <div class="card-header">
                        <h3>⚡ Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="actions-grid">
                            <a href="{{ route('employees.create') }}" class="action-card">
                                <span class="action-icon">👤</span>
                                <span class="action-label">Add Employee</span>
                            </a>
                            <a href="{{ route('shifts.create') }}" class="action-card" onclick="event.preventDefault(); document.getElementById('shiftModal').classList.add('show');">
                                <span class="action-icon">⏰</span>
                                <span class="action-label">Create Shift</span>
                            </a>
                            <a href="{{ route('shifts.index') }}" class="action-card">
                                <span class="action-icon">📋</span>
                                <span class="action-label">Assign Shifts</span>
                            </a>
                            <a href="{{ route('stock.create') }}" class="action-card">
                                <span class="action-icon">📦</span>
                                <span class="action-label">Add Stock</span>
                            </a>
                            <a href="{{ route('salaries.create') }}" class="action-card">
                                <span class="action-icon">💰</span>
                                <span class="action-label">Process Salary</span>
                            </a>
                            <a href="{{ route('employees.index') }}" class="action-card">
                                <span class="action-icon">👥</span>
                                <span class="action-label">Manage Team</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Two Column Layout -->
                <div class="grid-2">
                    <!-- Today's Shifts -->
                    <div class="card">
                        <div class="card-header">
                            <h3>📅 Today's Shifts</h3>
                            <a href="{{ route('shifts.schedule') }}" class="btn btn-outline btn-sm">View Schedule</a>
                        </div>
                        <div class="card-body no-pad">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Shift</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($todayShifts as $sa)
                                    <tr>
                                        <td style="font-weight: 600;">{{ $sa->user->name ?? 'N/A' }}</td>
                                        <td>{{ $sa->shift->name ?? 'N/A' }}</td>
                                        <td style="color: #64748b;">{{ $sa->shift->start_time ?? '-' }} - {{ $sa->shift->end_time ?? '-' }}</td>
                                        <td>
                                            @php
                                                $statusBadges = [
                                                    'present' => 'badge-green',
                                                    'late' => 'badge-yellow',
                                                    'absent' => 'badge-red',
                                                    'assigned' => 'badge-blue',
                                                ];
                                            @endphp
                                            <span class="badge {{ $statusBadges[$sa->status] ?? 'badge-gray' }}">{{ ucfirst($sa->status) }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 32px; color: #94a3b8;">No shifts assigned for today.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="card">
                        <div class="card-header">
                            <h3>📊 Recent Activity</h3>
                            <a href="{{ route('activity-logs.index') }}" class="btn btn-outline btn-sm">View All</a>
                        </div>
                        <div class="card-body">
                            @forelse($recentActivity->take(8) as $log)
                            <div class="activity-item">
                                <div class="activity-dot" style="background: {{ match(true) { str_contains($log->action, 'login') => '#2563eb', str_contains($log->action, 'clock') => '#16a34a', str_contains($log->action, 'create') => '#9333ea', str_contains($log->action, 'delete') => '#dc2626', default => '#64748b' }; }}"></div>
                                <div>
                                    <div class="activity-text"><strong>{{ $log->user->name ?? 'System' }}</strong> {{ $log->description }}</div>
                                    <div class="activity-time">{{ $log->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            @empty
                            <p style="text-align: center; padding: 32px; color: #94a3b8;">No recent activity.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Stock Alerts & Pending Salaries -->
                <div class="grid-2">
                    <!-- Stock Alerts -->
                    <div class="card">
                        <div class="card-header">
                            <h3>⚠️ Stock Alerts</h3>
                            <a href="{{ route('stock.index') }}" class="btn btn-outline btn-sm">View Stock</a>
                        </div>
                        <div class="card-body no-pad">
                            @if($stockAlerts->count() > 0)
                            <table>
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Type</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stockAlerts as $item)
                                    <tr>
                                        <td style="font-weight: 600;">{{ $item->name }}</td>
                                        <td><span class="badge badge-purple">{{ ucfirst(str_replace('_', ' ', $item->type)) }}</span></td>
                                        <td><span style="color: #dc2626; font-weight: 700;">{{ $item->quantity }} {{ $item->unit }}</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                            <p style="text-align: center; padding: 32px; color: #16a34a;">✅ All stock levels are healthy.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Pending Salaries -->
                    <div class="card">
                        <div class="card-header">
                            <h3>💰 Pending Salaries</h3>
                            <a href="{{ route('salaries.index') }}" class="btn btn-outline btn-sm">View All</a>
                        </div>
                        <div class="card-body">
                            <div style="text-align: center; padding: 20px 0;">
                                <div style="font-size: 48px; font-weight: 800; color: #d97706;">{{ $stats['pending_salaries'] ?? 0 }}</div>
                                <div style="font-size: 14px; color: #64748b; margin-top: 4px;">salaries awaiting payment</div>
                                <a href="{{ route('salaries.create') }}" class="btn btn-primary" style="margin-top: 20px;">Process Salaries →</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Overview -->
                <div class="card" style="margin-bottom: 32px;">
                    <div class="card-header">
                        <h3>👥 Employee Overview</h3>
                        <div style="display: flex; gap: 8px;">
                            <a href="{{ route('employees.index') }}" class="btn btn-outline btn-sm">View All</a>
                            <a href="{{ route('employees.create') }}" class="btn btn-primary btn-sm">+ Add New</a>
                        </div>
                    </div>
                    <div class="card-body no-pad">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $emp)
                                <tr>
                                    <td style="font-weight: 600;">{{ $emp->name }}</td>
                                    <td style="color: #64748b;">{{ $emp->email }}</td>
                                    <td>
                                        <span class="badge {{ $emp->role === 'manager' ? 'badge-blue' : 'badge-gray' }}">{{ ucfirst($emp->role) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $emp->onboarding_status === 'active' ? 'badge-green' : 'badge-red' }}">{{ ucfirst($emp->onboarding_status ?? 'Active') }}</span>
                                    </td>
                                    <td style="color: #64748b;">{{ $emp->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                                @if($employees->isEmpty())
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 32px; color: #94a3b8;">No employees found.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shift Creation Modal -->
    <div id="shiftModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3>⏰ Create New Shift</h3>
                <button class="modal-close" onclick="document.getElementById('shiftModal').classList.remove('show')">&times;</button>
            </div>
            <form method="POST" action="{{ route('shifts.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Shift Name</label>
                        <input type="text" name="name" required placeholder="e.g. Morning Shift, Night Shift">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Start Time</label>
                            <input type="time" name="start_time" required>
                        </div>
                        <div class="form-group">
                            <label>End Time</label>
                            <input type="time" name="end_time" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('shiftModal').classList.remove('show')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Shift</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast -->
    <div id="toast" class="toast"></div>

    <script>
        // Close modal on overlay click
        document.getElementById('shiftModal')?.addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('show');
        });

        // Close modal on Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('shiftModal')?.classList.remove('show');
            }
        });

        // Toast notification
        @if(session('success'))
            showToast('{{ session("success") }}', 'success');
        @endif
        @if(session('error'))
            showToast('{{ session("error") }}', 'error');
        @endif

        function showToast(message, type = '') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'toast ' + type + ' show';
            setTimeout(() => toast.classList.remove('show'), 3000);
        }
    </script>
</body>
</html>
