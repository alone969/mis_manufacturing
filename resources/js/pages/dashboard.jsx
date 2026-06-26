import { useState, useEffect } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { Input } from "@/components/ui/input";
import { Separator } from "@/components/ui/separator";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Button } from "@/components/ui/button";
import { authHeaders } from "@/lib/utils";

function getStatusVariant(status) {
  switch (status) {
    case "clocked_in": return "default";
    case "clocked_out": return "secondary";
    case "scheduled": return "outline";
    case "absent": return "destructive";
    default: return "default";
  }
}

function getStockVariant(quantity) {
  if (quantity < 5) return "destructive";
  if (quantity < 10) return "outline";
  return "default";
}

export default function Dashboard({ user, onLogout, onViewAccount, onNavigate }) {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [search, setSearch] = useState("");
  const [unreadMsgCount, setUnreadMsgCount] = useState(0);
  const [unreadNotifCount, setUnreadNotifCount] = useState(0);

  const isAdmin = user?.role === "admin";
  const isManager = user?.role === "manager" || isAdmin;

  useEffect(() => {
    fetch("/api/dashboard", {
      headers: authHeaders(),
      credentials: "same-origin",
    })
      .then((res) => {
        if (!res.ok) throw new Error("Failed to load dashboard");
        return res.json();
      })
      .then((d) => setData(d))
      .catch(() => setError("Could not load dashboard data."))
      .finally(() => setLoading(false));

    // Load badge counts
    fetch("/api/messages/unread-count", { headers: authHeaders(), credentials: "same-origin" })
      .then(r => r.json())
      .then(d => setUnreadMsgCount(d.unread_count || 0))
      .catch(() => {});

    fetch("/api/notifications/unread-count", { headers: authHeaders(), credentials: "same-origin" })
      .then(r => r.json())
      .then(d => setUnreadNotifCount(d.unread_count || 0))
      .catch(() => {});
  }, []);

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background">
        <p className="text-muted-foreground">Loading dashboard...</p>
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background p-4">
        <Card className="w-full max-w-md">
          <CardContent className="pt-6 text-center">
            <p className="text-destructive mb-4">{error}</p>
            <Button variant="outline" onClick={onLogout}>Logout</Button>
          </CardContent>
        </Card>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-background p-6 lg:p-8">
      <div className="max-w-7xl mx-auto space-y-8">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold tracking-tight">Dashboard</h1>
            <p className="text-muted-foreground">Welcome back{user ? `, ${user.name}` : ''}! Here's your overview.</p>
          </div>
          <div className="flex items-center gap-2 flex-wrap justify-end">
            {/* Primary navigation */}
            {isAdmin && <Button size="sm" onClick={() => onNavigate("employees")}>Employees</Button>}
            {isManager && <Button size="sm" onClick={() => onNavigate("shifts")}>Shifts</Button>}
            {isAdmin && <Button size="sm" onClick={() => onNavigate("stock")}>Stock</Button>}
            {isAdmin && <Button size="sm" onClick={() => onNavigate("salaries")}>Salaries</Button>}
            {isAdmin && <Button size="sm" variant="outline" onClick={() => onNavigate("activity")}>Activity Log</Button>}

            <Separator orientation="vertical" className="h-6" />

            {/* User tools */}
            <Button size="sm" variant="outline" onClick={() => onNavigate("messages")}>
              Messages
              {unreadMsgCount > 0 && <Badge variant="destructive" className="ml-1 text-xs px-1">{unreadMsgCount}</Badge>}
            </Button>
            <Button size="sm" variant="outline" onClick={() => onNavigate("notifications")}>
              Notifications
              {unreadNotifCount > 0 && <Badge variant="destructive" className="ml-1 text-xs px-1">{unreadNotifCount}</Badge>}
            </Button>
            <Button size="sm" variant="outline" onClick={() => onNavigate("device-logs")}>Devices</Button>
            <Button size="sm" variant="outline" onClick={() => onNavigate("settings")}>Settings</Button>

            <Separator orientation="vertical" className="h-6" />

            <Badge variant="outline" className="capitalize">{user?.role}</Badge>
            <Button size="sm" variant="outline" onClick={onViewAccount}>Account</Button>
            <Button size="sm" variant="outline" onClick={onLogout}>Logout</Button>
          </div>
        </div>

        {/* ── Admin Dashboard ────────────────────────────────────── */}
        {isAdmin && data?.stats && (
          <>
            <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
              <Card>
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                  <CardTitle className="text-sm font-medium">Total Employees</CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="text-2xl font-bold">{data.stats.total_employees}</div>
                </CardContent>
              </Card>
              <Card>
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                  <CardTitle className="text-sm font-medium">Active Shifts Today</CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="text-2xl font-bold">{data.stats.active_shifts_today}</div>
                </CardContent>
              </Card>
              <Card>
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                  <CardTitle className="text-sm font-medium">Total Stock Items</CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="text-2xl font-bold">{data.stats.total_stock_items}</div>
                </CardContent>
              </Card>
              <Card>
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                  <CardTitle className="text-sm font-medium">Pending Salaries</CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="text-2xl font-bold">{data.stats.pending_salaries}</div>
                </CardContent>
              </Card>
            </div>

            <div className="grid gap-6 lg:grid-cols-7">
              {/* Today's Shifts */}
              <Card className="lg:col-span-4">
                <CardHeader>
                  <CardTitle>Today's Shifts</CardTitle>
                  <CardDescription>Shift assignments for today.</CardDescription>
                </CardHeader>
                <CardContent>
                  {data.today_shifts?.length > 0 ? (
                    <Table>
                      <TableHeader>
                        <TableRow>
                          <TableHead>Employee</TableHead>
                          <TableHead>Shift</TableHead>
                          <TableHead>Time</TableHead>
                          <TableHead>Status</TableHead>
                        </TableRow>
                      </TableHeader>
                      <TableBody>
                        {data.today_shifts.map((s, i) => (
                          <TableRow key={i}>
                            <TableCell className="font-medium">{s.employee}</TableCell>
                            <TableCell>{s.shift}</TableCell>
                            <TableCell className="text-sm text-muted-foreground">{s.start} – {s.end}</TableCell>
                            <TableCell>
                              <Badge variant={getStatusVariant(s.status)} className="capitalize">{s.status.replace("_", " ")}</Badge>
                            </TableCell>
                          </TableRow>
                        ))}
                      </TableBody>
                    </Table>
                  ) : (
                    <p className="text-sm text-muted-foreground">No shifts scheduled for today.</p>
                  )}
                </CardContent>
              </Card>

              {/* Stock Alerts */}
              <Card className="lg:col-span-3">
                <CardHeader>
                  <CardTitle>Stock Alerts</CardTitle>
                  <CardDescription>Items running low.</CardDescription>
                </CardHeader>
                <CardContent>
                  {data.stock_alerts?.length > 0 ? (
                    <div className="space-y-4">
                      {data.stock_alerts.map((item, i) => (
                        <div key={i} className="flex items-center justify-between">
                          <div>
                            <p className="text-sm font-medium">{item.name}</p>
                            <p className="text-xs text-muted-foreground">{item.type.replace("_", " ")}</p>
                          </div>
                          <Badge variant={getStockVariant(item.quantity)}>
                            {item.quantity} {item.unit}
                          </Badge>
                        </div>
                      ))}
                    </div>
                  ) : (
                    <p className="text-sm text-muted-foreground">All stock levels are adequate.</p>
                  )}
                </CardContent>
              </Card>
            </div>

            {/* Recent Activity */}
            <Card>
              <CardHeader>
                <CardTitle>Recent Activity</CardTitle>
                <CardDescription>Latest actions across the system.</CardDescription>
              </CardHeader>
              <CardContent>
                {data.recent_activity?.length > 0 ? (
                  <div className="space-y-4">
                    {data.recent_activity.map((a, i) => (
                      <div key={i} className="flex items-center gap-4">
                        <Avatar className="h-9 w-9">
                          <AvatarFallback>{a.user?.split(" ").map(n => n[0]).join("")}</AvatarFallback>
                        </Avatar>
                        <div className="flex-1 space-y-1">
                          <p className="text-sm font-medium leading-none">
                            {a.user}{" "}
                            <span className="text-muted-foreground font-normal">{a.description || a.action}</span>
                          </p>
                          <p className="text-sm text-muted-foreground">{a.time}</p>
                        </div>
                      </div>
                    ))}
                  </div>
                ) : (
                  <p className="text-sm text-muted-foreground">No recent activity.</p>
                )}
              </CardContent>
            </Card>
          </>
        )}

        {/* ── Manager Dashboard ──────────────────────────────────── */}
        {isManager && !isAdmin && data?.stats && (
          <>
            <div className="grid gap-4 md:grid-cols-3">
              <Card>
                <CardHeader><CardTitle className="text-sm font-medium">Team Members</CardTitle></CardHeader>
                <CardContent><div className="text-2xl font-bold">{data.stats.team_members}</div></CardContent>
              </Card>
              <Card>
                <CardHeader><CardTitle className="text-sm font-medium">Active Shifts Today</CardTitle></CardHeader>
                <CardContent><div className="text-2xl font-bold">{data.stats.active_shifts_today}</div></CardContent>
              </Card>
              <Card>
                <CardHeader><CardTitle className="text-sm font-medium">Total Stock</CardTitle></CardHeader>
                <CardContent><div className="text-2xl font-bold">{data.stats.total_stock_items}</div></CardContent>
              </Card>
            </div>
            {data.today_shifts?.length > 0 && (
              <Card>
                <CardHeader><CardTitle>Today's Shifts</CardTitle></CardHeader>
                <CardContent>
                  <Table>
                    <TableHeader>
                      <TableRow>
                        <TableHead>Employee</TableHead>
                        <TableHead>Shift</TableHead>
                        <TableHead>Time</TableHead>
                        <TableHead>Status</TableHead>
                      </TableRow>
                    </TableHeader>
                    <TableBody>
                      {data.today_shifts.map((s, i) => (
                        <TableRow key={i}>
                          <TableCell className="font-medium">{s.employee}</TableCell>
                          <TableCell>{s.shift}</TableCell>
                          <TableCell className="text-sm text-muted-foreground">{s.start} – {s.end}</TableCell>
                          <TableCell><Badge variant={getStatusVariant(s.status)} className="capitalize">{s.status.replace("_", " ")}</Badge></TableCell>
                        </TableRow>
                      ))}
                    </TableBody>
                  </Table>
                </CardContent>
              </Card>
            )}
          </>
        )}

        {/* ── Employee Dashboard ─────────────────────────────────── */}
        {!isManager && data?.stats && (
          <>
            <div className="grid gap-4 md:grid-cols-3">
              <Card>
                <CardHeader><CardTitle className="text-sm font-medium">Today's Shift</CardTitle></CardHeader>
                <CardContent><div className="text-2xl font-bold">{data.stats.today_shift}</div></CardContent>
              </Card>
              <Card>
                <CardHeader><CardTitle className="text-sm font-medium">Attendance This Week</CardTitle></CardHeader>
                <CardContent>
                  <div className="text-2xl font-bold">{data.stats.attendance_this_week}/{data.stats.total_shifts_this_week}</div>
                </CardContent>
              </Card>
              <Card>
                <CardHeader><CardTitle className="text-sm font-medium">My Shift</CardTitle></CardHeader>
                <CardContent>
                  {data.today_shift ? (
                    <div className="space-y-2">
                      <p className="text-lg font-semibold">{data.today_shift.shift}</p>
                      <p className="text-sm text-muted-foreground">{data.today_shift.start} – {data.today_shift.end}</p>
                      <Badge variant={getStatusVariant(data.today_shift.status)} className="capitalize">
                        {data.today_shift.status.replace("_", " ")}
                      </Badge>
                      {data.today_shift.status === "scheduled" && (
                        <Button size="sm" onClick={() => clockIn(data.today_shift.assignment_id)}>
                          Clock In
                        </Button>
                      )}
                      {data.today_shift.status === "clocked_in" && (
                        <Button size="sm" variant="outline" onClick={() => clockOut(data.today_shift.assignment_id)}>
                          Clock Out
                        </Button>
                      )}
                    </div>
                  ) : (
                    <p className="text-muted-foreground">No shift today.</p>
                  )}
                </CardContent>
              </Card>
            </div>

            {data.recent_attendance?.length > 0 && (
              <Card>
                <CardHeader><CardTitle>Recent Attendance</CardTitle></CardHeader>
                <CardContent>
                  <Table>
                    <TableHeader>
                      <TableRow>
                        <TableHead>Date</TableHead>
                        <TableHead>Shift</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Clock In</TableHead>
                        <TableHead>Clock Out</TableHead>
                      </TableRow>
                    </TableHeader>
                    <TableBody>
                      {data.recent_attendance.map((a, i) => (
                        <TableRow key={i}>
                          <TableCell>{a.date}</TableCell>
                          <TableCell>{a.shift}</TableCell>
                          <TableCell><Badge variant={getStatusVariant(a.status)} className="capitalize">{a.status.replace("_", " ")}</Badge></TableCell>
                          <TableCell>{a.clock_in || "—"}</TableCell>
                          <TableCell>{a.clock_out || "—"}</TableCell>
                        </TableRow>
                      ))}
                    </TableBody>
                  </Table>
                </CardContent>
              </Card>
            )}
          </>
        )}

        <Separator />
        <div className="flex items-center justify-between text-sm text-muted-foreground">
          <p>MIS Manufacturing — {user?.role}</p>
        </div>
      </div>
    </div>
  );

  function clockIn(assignmentId) {
    fetch(`/api/shifts/${assignmentId}/clock-in`, {
      method: "POST",
      headers: authHeaders(),
      credentials: "same-origin",
    })
      .then((res) => res.json())
      .then(() => window.location.reload())
      .catch(() => {});
  }

  function clockOut(assignmentId) {
    fetch(`/api/shifts/${assignmentId}/clock-out`, {
      method: "POST",
      headers: authHeaders(),
      credentials: "same-origin",
    })
      .then((res) => res.json())
      .then(() => window.location.reload())
      .catch(() => {});
  }
}
