import { useState, useEffect } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Badge } from "@/components/ui/badge";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Separator } from "@/components/ui/separator";
import { authHeaders } from "@/lib/utils";

export default function Shifts({ onBack, user }) {
  const canManage = user?.role === "admin" || user?.role === "manager";
  const [shifts, setShifts] = useState([]);
  const [assignments, setAssignments] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [selectedDate, setSelectedDate] = useState(new Date().toISOString().split("T")[0]);
  const [form, setForm] = useState({ name: "", start_time: "", end_time: "" });
  const [error, setError] = useState("");
  const [users, setUsers] = useState([]);
  const [assignForm, setAssignForm] = useState({ shift_id: "", user_id: "", date: "" });

  useEffect(() => {
    Promise.all([
      fetch("/api/shifts", { headers: authHeaders(), credentials: "same-origin" }).then(r => r.json()),
      fetch(`/api/shifts/assignments/${selectedDate}`, { headers: authHeaders(), credentials: "same-origin" }).then(r => r.json()),
      fetch("/api/admin/users", { headers: authHeaders(), credentials: "same-origin" }).then(r => r.json()),
    ]).then(([s, a, u]) => {
      setShifts(s);
      setAssignments(a);
      setUsers(u);
    }).catch(() => setError("Failed to load data."))
      .finally(() => setLoading(false));
  }, [selectedDate]);

  const createShift = async (e) => {
    e.preventDefault();
    setError("");
    const res = await fetch("/api/shifts", {
      method: "POST",
      headers: { ...authHeaders(), "Content-Type": "application/json" },
      credentials: "same-origin",
      body: JSON.stringify(form),
    });
    if (!res.ok) {
      const data = await res.json();
      setError(data.message || "Failed to create shift.");
      return;
    }
    const newShift = await res.json();
    setShifts([newShift, ...shifts]);
    setForm({ name: "", start_time: "", end_time: "" });
    setShowForm(false);
  };

  const assignEmployee = async (e) => {
    e.preventDefault();
    setError("");
    const res = await fetch(`/api/shifts/${assignForm.shift_id}/assign`, {
      method: "POST",
      headers: { ...authHeaders(), "Content-Type": "application/json" },
      credentials: "same-origin",
      body: JSON.stringify({ user_id: assignForm.user_id, date: assignForm.date || selectedDate }),
    });
    if (!res.ok) {
      const data = await res.json();
      setError(data.message || "Failed to assign.");
      return;
    }
    const a = await res.json();
    setAssignments([...assignments, a]);
    setAssignForm({ shift_id: "", user_id: "", date: "" });
  };

  const deleteShift = async (id) => {
    if (!confirm("Delete this shift?")) return;
    await fetch(`/api/shifts/${id}`, { method: "DELETE", headers: authHeaders(), credentials: "same-origin" });
    setShifts(shifts.filter(s => s.id !== id));
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background">
        <p className="text-muted-foreground">Loading shifts...</p>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-background p-6 lg:p-8">
      <div className="max-w-5xl mx-auto space-y-8">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold tracking-tight">Shift Management</h1>
            <p className="text-muted-foreground">Create, assign, and manage shifts.</p>
          </div>
          <Button variant="outline" onClick={onBack}>← Back</Button>
        </div>

        {error && (
          <div className="p-3 text-sm text-destructive bg-destructive/10 border border-destructive/20 rounded-md">{error}</div>
        )}

        {/* Create Shift Form */}
        <Card>
          <CardHeader className="flex flex-row items-center justify-between">
            <div>
              <CardTitle>Shifts</CardTitle>
              <CardDescription>{shifts.length} shift(s) defined.</CardDescription>
            </div>
            {canManage && <Button size="sm" onClick={() => setShowForm(!showForm)}>
              {showForm ? "Cancel" : "+ New Shift"}
            </Button>}
          </CardHeader>
          <CardContent>
            {showForm && (
              <form onSubmit={createShift} className="grid grid-cols-4 gap-4 mb-6 p-4 bg-muted/50 rounded-lg">
                <div className="space-y-1">
                  <Label>Name</Label>
                  <Input value={form.name} onChange={e => setForm({ ...form, name: e.target.value })} required placeholder="e.g. Morning" />
                </div>
                <div className="space-y-1">
                  <Label>Start Time</Label>
                  <Input type="time" value={form.start_time} onChange={e => setForm({ ...form, start_time: e.target.value })} required />
                </div>
                <div className="space-y-1">
                  <Label>End Time</Label>
                  <Input type="time" value={form.end_time} onChange={e => setForm({ ...form, end_time: e.target.value })} required />
                </div>
                <div className="flex items-end">
                  <Button type="submit" className="w-full">Create</Button>
                </div>
              </form>
            )}

            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Name</TableHead>
                  <TableHead>Start</TableHead>
                  <TableHead>End</TableHead>
                  <TableHead>Assignments</TableHead>
                  <TableHead className="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {shifts.map(s => (
                  <TableRow key={s.id}>
                    <TableCell className="font-medium">{s.name}</TableCell>
                    <TableCell>{s.start_time}</TableCell>
                    <TableCell>{s.end_time}</TableCell>
                    <TableCell><Badge variant="secondary">{s.assignments_count ?? 0}</Badge></TableCell>
                    <TableCell className="text-right">
                      {canManage && <Button variant="ghost" size="sm" className="text-destructive" onClick={() => deleteShift(s.id)}>Delete</Button>}
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </CardContent>
        </Card>

        <Separator />

        {/* Assign Employee */}
        {canManage && <Card>
          <CardHeader>
            <CardTitle>Assign Employee to Shift</CardTitle>
            <CardDescription>Select a shift, employee, and date.</CardDescription>
          </CardHeader>
          <CardContent>
            <form onSubmit={assignEmployee} className="grid grid-cols-4 gap-4">
              <div className="space-y-1">
                <Label>Shift</Label>
                <select
                  value={assignForm.shift_id}
                  onChange={e => setAssignForm({ ...assignForm, shift_id: e.target.value })}
                  className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm"
                  required
                >
                  <option value="">Select shift...</option>
                  {shifts.map(s => <option key={s.id} value={s.id}>{s.name} ({s.start_time}–{s.end_time})</option>)}
                </select>
              </div>
              <div className="space-y-1">
                <Label>Employee</Label>
                <select
                  value={assignForm.user_id}
                  onChange={e => setAssignForm({ ...assignForm, user_id: e.target.value })}
                  className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm"
                  required
                >
                  <option value="">Select employee...</option>
                  {users.map(u => <option key={u.id} value={u.id}>{u.name}</option>)}
                </select>
              </div>
              <div className="space-y-1">
                <Label>Date</Label>
                <Input type="date" value={assignForm.date || selectedDate} onChange={e => setAssignForm({ ...assignForm, date: e.target.value })} required />
              </div>
              <div className="flex items-end">
                <Button type="submit" className="w-full">Assign</Button>
              </div>
            </form>
          </CardContent>
        </Card>}

        {/* Today's Assignments */}
        <Card>
          <CardHeader className="flex flex-row items-center justify-between">
            <div>
              <CardTitle>Assignments for {selectedDate}</CardTitle>
            </div>
            <Input
              type="date"
              value={selectedDate}
              onChange={e => setSelectedDate(e.target.value)}
              className="w-48"
            />
          </CardHeader>
          <CardContent>
            {assignments.length > 0 ? (
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Employee</TableHead>
                    <TableHead>Shift</TableHead>
                    <TableHead>Time</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Clock In</TableHead>
                    <TableHead>Clock Out</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {assignments.map(a => (
                    <TableRow key={a.id}>
                      <TableCell className="font-medium">{a.user?.name}</TableCell>
                      <TableCell>{a.shift?.name}</TableCell>
                      <TableCell className="text-sm text-muted-foreground">{a.shift?.start_time} – {a.shift?.end_time}</TableCell>
                      <TableCell>
                        <Badge variant={
                          a.status === "clocked_in" ? "default" :
                          a.status === "clocked_out" ? "secondary" :
                          a.status === "absent" ? "destructive" : "outline"
                        } className="capitalize">{a.status.replace("_", " ")}</Badge>
                      </TableCell>
                      <TableCell>{a.clock_in ? new Date(a.clock_in).toLocaleTimeString() : "—"}</TableCell>
                      <TableCell>{a.clock_out ? new Date(a.clock_out).toLocaleTimeString() : "—"}</TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            ) : (
              <p className="text-sm text-muted-foreground">No assignments for this date.</p>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
