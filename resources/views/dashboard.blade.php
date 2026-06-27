<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Dashboard</h2>
            <span class="text-sm text-gray-500">{{ now()->format('l, F j, Y') }}</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @php $role = Auth::user()->role; @endphp

            {{-- Welcome Banner --}}
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl p-6 mb-8 text-white">
                <h3 class="text-2xl font-bold">Welcome back, {{ Auth::user()->name }}!</h3>
                <p class="text-blue-100 mt-1">Here's what's happening in your manufacturing facility today.</p>
            </div>

            {{-- Admin Dashboard --}}
            @if($role === 'admin')
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="text-3xl mb-2">👥</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_employees'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Employees</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="text-3xl mb-2">📋</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_managers'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Managers</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="text-3xl mb-2">⏰</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active_shifts_today'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Active Shifts</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="text-3xl mb-2">📦</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_stock_items'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Stock Units</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="text-3xl mb-2">💰</div>
                        <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending_salaries'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Pending Salaries</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="text-3xl mb-2">🛡️</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_admins'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Admins</div>
                    </div>
                </div>

                {{-- Shift Management Panel --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Today's Shift Assignments</h3>
                                <button onclick="openShiftModal()" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition">
                                    + Assign Shift
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            @if($todayShifts->count() > 0)
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead><tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Employee</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Shift</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Time</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                                    </tr></thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                        @foreach($todayShifts as $sa)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $sa->user->name ?? 'N/A' }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $sa->shift->name ?? 'N/A' }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-500">{{ $sa->shift->start_time ?? '-' }} - {{ $sa->shift->end_time ?? '-' }}</td>
                                            <td class="px-4 py-3">
                                                @php
                                                    $statusColors = [
                                                        'present' => 'bg-green-100 text-green-800',
                                                        'late' => 'bg-yellow-100 text-yellow-800',
                                                        'absent' => 'bg-red-100 text-red-800',
                                                        'assigned' => 'bg-blue-100 text-blue-800',
                                                    ];
                                                @endphp
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$sa->status] ?? 'bg-gray-100 text-gray-800' }}">{{ ucfirst($sa->status) }}</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-gray-500 text-center py-8">No shift assignments for today.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Quick Actions --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('employees.create') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-3 rounded-lg font-semibold transition">👥 Add Employee</a>
                            <a href="{{ route('shifts.create') }}" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center py-3 rounded-lg font-semibold transition">⏰ Create Shift</a>
                            <a href="{{ route('stock.create') }}" class="block w-full bg-purple-600 hover:bg-purple-700 text-white text-center py-3 rounded-lg font-semibold transition">📦 Add Stock Item</a>
                            <a href="{{ route('salaries.create') }}" class="block w-full bg-yellow-600 hover:bg-yellow-700 text-white text-center py-3 rounded-lg font-semibold transition">💰 Process Salary</a>
                            <a href="{{ route('activity-logs.index') }}" class="block w-full bg-gray-600 hover:bg-gray-700 text-white text-center py-3 rounded-lg font-semibold transition">📊 View Logs</a>
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <h4 class="text-sm font-semibold text-gray-500 uppercase mb-3">Recent Activity</h4>
                            @forelse($recentActivity->take(5) as $log)
                            <div class="flex items-start gap-3 py-2">
                                <div class="w-2 h-2 rounded-full bg-blue-500 mt-2 flex-shrink-0"></div>
                                <div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $log->user->name ?? 'System' }}: {{ $log->description }}</p>
                                    <p class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            @empty
                            <p class="text-sm text-gray-400">No recent activity.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif

            {{-- Manager Dashboard --}}
            @if($role === 'manager')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="text-3xl mb-2">👥</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['team_members'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Team Members</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="text-3xl mb-2">⏰</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active_shifts_today'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Active Shifts Today</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="text-3xl mb-2">📦</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_stock_items'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Stock Units</div>
                    </div>
                </div>
            @endif

            {{-- Employee Dashboard --}}
            @if($role === 'employee')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="text-3xl mb-2">⏰</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['today_shift'] ?? 'No shift' }}</div>
                        <div class="text-sm text-gray-500">Today's Shift</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="text-3xl mb-2">✅</div>
                        <div class="text-2xl font-bold text-green-600">{{ $stats['attendance_this_week'] ?? 0 }}/{{ $stats['total_shifts_this_week'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">This Week's Attendance</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="text-3xl mb-2">📅</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_shifts_this_week'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Total Shifts This Week</div>
                    </div>
                </div>

                {{-- Clock In/Out --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 mb-8">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">⚡ Quick Actions</h3>
                    <div class="flex gap-4">
                        <form method="POST" action="{{ route('attendance.clock-in') }}">
                            @csrf
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg transition text-lg">🕐 Clock In</button>
                        </form>
                        <form method="POST" action="{{ route('attendance.clock-out') }}">
                            @csrf
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-lg transition text-lg">🕕 Clock Out</button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Today's Shifts Table (all roles) --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📅 Today's Shift Schedule</h3>
                    @if($todayShifts->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead><tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Employee</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Shift</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Start</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">End</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            </tr></thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($todayShifts as $sa)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $sa->user->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $sa->shift->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $sa->shift->start_time ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $sa->shift->end_time ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        @php $statusColors = ['present' => 'bg-green-100 text-green-800', 'late' => 'bg-yellow-100 text-yellow-800', 'absent' => 'bg-red-100 text-red-800', 'assigned' => 'bg-blue-100 text-blue-800']; @endphp
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$sa->status] ?? 'bg-gray-100 text-gray-800' }}">{{ ucfirst($sa->status) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500 text-center py-8">No shifts scheduled for today.</p>
                    @endif
                </div>
            </div>

            {{-- Stock Alerts --}}
            @if($stockAlerts->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">⚠️ Stock Alerts (Low Stock)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($stockAlerts as $item)
                        <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item->name }}</p>
                                <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $item->type)) }}</p>
                            </div>
                            <span class="text-sm font-bold text-red-600">{{ $item->quantity }} {{ $item->unit }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Shift Assignment Modal --}}
    @if($role === 'admin' || $role === 'manager')
    <div id="shiftModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-lg mx-4">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Assign Shift to Employee</h3>
                    <button onclick="closeShiftModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                </div>
            </div>
            <form method="POST" action="{{ route('shifts.store') }}">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Shift Name</label>
                        <input type="text" name="name" required placeholder="e.g. Morning Shift" class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Start Time</label>
                            <input type="time" name="start_time" required class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">End Time</label>
                            <input type="time" name="end_time" required class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                    <button type="button" onclick="closeShiftModal()" class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">Create Shift</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        function openShiftModal() {
            document.getElementById('shiftModal').classList.remove('hidden');
            document.getElementById('shiftModal').classList.add('flex');
        }
        function closeShiftModal() {
            document.getElementById('shiftModal').classList.add('hidden');
            document.getElementById('shiftModal').classList.remove('flex');
        }
        document.getElementById('shiftModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeShiftModal();
        });
    </script>
    @endpush
</x-app-layout>
