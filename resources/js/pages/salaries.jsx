import { useState, useEffect } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Badge } from "@/components/ui/badge";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { authHeaders } from "@/lib/utils";

export default function Salaries({ onBack }) {
  const [salaries, setSalaries] = useState([]);
  const [summary, setSummary] = useState(null);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [error, setError] = useState("");
  const [users, setUsers] = useState([]);
  const [form, setForm] = useState({ user_id: "", amount: "", period_start: "", period_end: "" });

  useEffect(() => {
    Promise.all([
      fetch("/api/salaries", { headers: authHeaders(), credentials: "same-origin" }).then(r => r.json()),
      fetch("/api/salaries/summary", { headers: authHeaders(), credentials: "same-origin" }).then(r => r.json()),
      fetch("/api/admin/users", { headers: authHeaders(), credentials: "same-origin" }).then(r => r.json()),
    ]).then(([s, sm, u]) => {
      setSalaries(s);
      setSummary(sm);
      setUsers(u);
    }).catch(() => setError("Failed to load salary data."))
      .finally(() => setLoading(false));
  }, []);

  const createSalary = async (e) => {
    e.preventDefault();
    setError("");
    const res = await fetch("/api/salaries", {
      method: "POST",
      headers: { ...authHeaders(), "Content-Type": "application/json" },
      credentials: "same-origin",
      body: JSON.stringify({ ...form, amount: parseFloat(form.amount) }),
    });
    if (!res.ok) {
      const data = await res.json();
      setError(data.message || "Failed to create salary record.");
      return;
    }
    const newSalary = await res.json();
    setSalaries([newSalary, ...salaries]);
    setForm({ user_id: "", amount: "", period_start: "", period_end: "" });
    setShowForm(false);
    // Refresh summary
    fetch("/api/salaries/summary", { headers: authHeaders(), credentials: "same-origin" })
      .then(r => r.json())
      .then(s => setSummary(s));
  };

  const markPaid = async (id) => {
    const res = await fetch(`/api/salaries/${id}/pay`, {
      method: "PUT",
      headers: authHeaders(),
      credentials: "same-origin",
    });
    if (res.ok) {
      const updated = await res.json();
      setSalaries(salaries.map(s => s.id === id ? updated : s));
      fetch("/api/salaries/summary", { headers: authHeaders(), credentials: "same-origin" })
        .then(r => r.json())
        .then(s => setSummary(s));
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background">
        <p className="text-muted-foreground">Loading salaries...</p>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-background p-6 lg:p-8">
      <div className="max-w-5xl mx-auto space-y-8">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold tracking-tight">Salary Management</h1>
            <p className="text-muted-foreground">Process and track salary payments.</p>
          </div>
          <Button variant="outline" onClick={onBack}>← Back</Button>
        </div>

        {error && (
          <div className="p-3 text-sm text-destructive bg-destructive/10 border border-destructive/20 rounded-md">{error}</div>
        )}

        {/* Summary Cards */}
        {summary && (
          <div className="grid gap-4 md:grid-cols-4">
            <Card>
              <CardHeader><CardTitle className="text-sm font-medium">Pending Total</CardTitle></CardHeader>
              <CardContent><div className="text-2xl font-bold">${Number(summary.total_pending).toLocaleString()}</div></CardContent>
            </Card>
            <Card>
              <CardHeader><CardTitle className="text-sm font-medium">Paid Total</CardTitle></CardHeader>
              <CardContent><div className="text-2xl font-bold">${Number(summary.total_paid).toLocaleString()}</div></CardContent>
            </Card>
            <Card>
              <CardHeader><CardTitle className="text-sm font-medium">Pending Records</CardTitle></CardHeader>
              <CardContent><div className="text-2xl font-bold">{summary.pending_count}</div></CardContent>
            </Card>
            <Card>
              <CardHeader><CardTitle className="text-sm font-medium">Paid Records</CardTitle></CardHeader>
              <CardContent><div className="text-2xl font-bold">{summary.paid_count}</div></CardContent>
            </Card>
          </div>
        )}

        {/* Create Salary */}
        <Card>
          <CardHeader className="flex flex-row items-center justify-between">
            <div>
              <CardTitle>Salary Records</CardTitle>
              <CardDescription>{salaries.length} record(s).</CardDescription>
            </div>
            <Button size="sm" onClick={() => setShowForm(!showForm)}>
              {showForm ? "Cancel" : "+ New Record"}
            </Button>
          </CardHeader>
          <CardContent>
            {showForm && (
              <form onSubmit={createSalary} className="grid grid-cols-5 gap-4 mb-6 p-4 bg-muted/50 rounded-lg">
                <div className="space-y-1">
                  <Label>Employee</Label>
                  <select value={form.user_id} onChange={e => setForm({ ...form, user_id: e.target.value })} className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm" required>
                    <option value="">Select...</option>
                    {users.map(u => <option key={u.id} value={u.id}>{u.name}</option>)}
                  </select>
                </div>
                <div className="space-y-1">
                  <Label>Amount ($)</Label>
                  <Input type="number" step="0.01" min="0" value={form.amount} onChange={e => setForm({ ...form, amount: e.target.value })} required />
                </div>
                <div className="space-y-1">
                  <Label>Period Start</Label>
                  <Input type="date" value={form.period_start} onChange={e => setForm({ ...form, period_start: e.target.value })} required />
                </div>
                <div className="space-y-1">
                  <Label>Period End</Label>
                  <Input type="date" value={form.period_end} onChange={e => setForm({ ...form, period_end: e.target.value })} required />
                </div>
                <div className="flex items-end">
                  <Button type="submit" className="w-full">Create</Button>
                </div>
              </form>
            )}

            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Employee</TableHead>
                  <TableHead>Amount</TableHead>
                  <TableHead>Period</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead>Paid At</TableHead>
                  <TableHead className="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {salaries.map(s => (
                  <TableRow key={s.id}>
                    <TableCell className="font-medium">{s.user?.name}</TableCell>
                    <TableCell>${Number(s.amount).toLocaleString()}</TableCell>
                    <TableCell className="text-sm text-muted-foreground">
                      {s.period_start} – {s.period_end}
                    </TableCell>
                    <TableCell>
                      <Badge variant={s.status === "paid" ? "default" : s.status === "pending" ? "outline" : "destructive"} className="capitalize">{s.status}</Badge>
                    </TableCell>
                    <TableCell className="text-sm text-muted-foreground">{s.paid_at ? new Date(s.paid_at).toLocaleDateString() : "—"}</TableCell>
                    <TableCell className="text-right">
                      {s.status === "pending" && (
                        <Button size="sm" onClick={() => markPaid(s.id)}>Mark Paid</Button>
                      )}
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
            {salaries.length === 0 && (
              <p className="text-sm text-muted-foreground text-center py-4">No salary records found.</p>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
