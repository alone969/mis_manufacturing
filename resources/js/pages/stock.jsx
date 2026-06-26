import { useState, useEffect } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Badge } from "@/components/ui/badge";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { authHeaders } from "@/lib/utils";

export default function Stock({ onBack, user }) {
  const isAdmin = user?.role === "admin";
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [error, setError] = useState("");
  const [filter, setFilter] = useState("");
  const [form, setForm] = useState({ name: "", type: "raw_material", quantity: "", unit: "" });

  useEffect(() => {
    const url = filter ? `/api/stock?type=${filter}` : "/api/stock";
    fetch(url, { headers: authHeaders(), credentials: "same-origin" })
      .then(r => r.json())
      .then(d => setItems(d))
      .catch(() => setError("Failed to load stock."))
      .finally(() => setLoading(false));
  }, [filter]);

  const createItem = async (e) => {
    e.preventDefault();
    setError("");
    const res = await fetch("/api/stock", {
      method: "POST",
      headers: { ...authHeaders(), "Content-Type": "application/json" },
      credentials: "same-origin",
      body: JSON.stringify({ ...form, quantity: parseFloat(form.quantity) }),
    });
    if (!res.ok) {
      const data = await res.json();
      setError(data.message || "Failed to create item.");
      return;
    }
    const newItem = await res.json();
    setItems([newItem, ...items]);
    setForm({ name: "", type: "raw_material", quantity: "", unit: "" });
    setShowForm(false);
  };

  const updateQuantity = async (id, newQty) => {
    const res = await fetch(`/api/stock/${id}`, {
      method: "PUT",
      headers: { ...authHeaders(), "Content-Type": "application/json" },
      credentials: "same-origin",
      body: JSON.stringify({ quantity: newQty }),
    });
    if (res.ok) {
      const updated = await res.json();
      setItems(items.map(i => i.id === id ? updated : i));
    }
  };

  const deleteItem = async (id) => {
    if (!confirm("Delete this stock item?")) return;
    await fetch(`/api/stock/${id}`, { method: "DELETE", headers: authHeaders(), credentials: "same-origin" });
    setItems(items.filter(i => i.id !== id));
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background">
        <p className="text-muted-foreground">Loading stock...</p>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-background p-6 lg:p-8">
      <div className="max-w-5xl mx-auto space-y-8">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold tracking-tight">Stock Management</h1>
            <p className="text-muted-foreground">Track raw materials and finished goods.</p>
          </div>
          <Button variant="outline" onClick={onBack}>← Back</Button>
        </div>

        {error && (
          <div className="p-3 text-sm text-destructive bg-destructive/10 border border-destructive/20 rounded-md">{error}</div>
        )}

        <Card>
          <CardHeader className="flex flex-row items-center justify-between">
            <div className="flex items-center gap-4">
              <CardTitle>Inventory</CardTitle>
              <div className="flex gap-2">
                <Badge variant={filter === "" ? "default" : "outline"} className="cursor-pointer" onClick={() => setFilter("")}>All</Badge>
                <Badge variant={filter === "raw_material" ? "default" : "outline"} className="cursor-pointer" onClick={() => setFilter("raw_material")}>Raw Materials</Badge>
                <Badge variant={filter === "finished_good" ? "default" : "outline"} className="cursor-pointer" onClick={() => setFilter("finished_good")}>Finished Goods</Badge>
              </div>
            </div>
            {isAdmin && <Button size="sm" onClick={() => setShowForm(!showForm)}>
              {showForm ? "Cancel" : "+ New Item"}
            </Button>}
          </CardHeader>
          <CardContent>
            {showForm && (
              <form onSubmit={createItem} className="grid grid-cols-5 gap-4 mb-6 p-4 bg-muted/50 rounded-lg">
                <div className="space-y-1">
                  <Label>Name</Label>
                  <Input value={form.name} onChange={e => setForm({ ...form, name: e.target.value })} required placeholder="e.g. Cotton Roll" />
                </div>
                <div className="space-y-1">
                  <Label>Type</Label>
                  <select value={form.type} onChange={e => setForm({ ...form, type: e.target.value })} className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm">
                    <option value="raw_material">Raw Material</option>
                    <option value="finished_good">Finished Good</option>
                  </select>
                </div>
                <div className="space-y-1">
                  <Label>Quantity</Label>
                  <Input type="number" step="0.01" min="0" value={form.quantity} onChange={e => setForm({ ...form, quantity: e.target.value })} required />
                </div>
                <div className="space-y-1">
                  <Label>Unit</Label>
                  <Input value={form.unit} onChange={e => setForm({ ...form, unit: e.target.value })} required placeholder="e.g. kg, meters, pcs" />
                </div>
                <div className="flex items-end">
                  <Button type="submit" className="w-full">Add</Button>
                </div>
              </form>
            )}

            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Name</TableHead>
                  <TableHead>Type</TableHead>
                  <TableHead>Quantity</TableHead>
                  <TableHead>Unit</TableHead>
                  <TableHead>Updated By</TableHead>
                  <TableHead className="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {items.map(item => (
                  <TableRow key={item.id}>
                    <TableCell className="font-medium">{item.name}</TableCell>
                    <TableCell>
                      <Badge variant="secondary" className="capitalize">{item.type.replace("_", " ")}</Badge>
                    </TableCell>
                    <TableCell>
                      <div className="flex items-center gap-2">
                        <Badge variant={item.quantity < 5 ? "destructive" : item.quantity < 10 ? "outline" : "default"}>
                          {item.quantity}
                        </Badge>
                        {isAdmin && <Button variant="ghost" size="sm" onClick={() => updateQuantity(item.id, item.quantity + 1)}>+</Button>}
                        {isAdmin && <Button variant="ghost" size="sm" onClick={() => updateQuantity(item.id, Math.max(0, item.quantity - 1))}>−</Button>}
                      </div>
                    </TableCell>
                    <TableCell>{item.unit}</TableCell>
                    <TableCell className="text-sm text-muted-foreground">{item.updatedBy?.name || "—"}</TableCell>
                    <TableCell className="text-right">
                      {isAdmin && <Button variant="ghost" size="sm" className="text-destructive" onClick={() => deleteItem(item.id)}>Delete</Button>}
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
            {items.length === 0 && (
              <p className="text-sm text-muted-foreground text-center py-4">No stock items found.</p>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
