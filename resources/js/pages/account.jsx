import { useState, useEffect } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Separator } from "@/components/ui/separator";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { authHeaders } from "@/lib/utils";

export default function Account({ onBack }) {
  const [account, setAccount] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [editing, setEditing] = useState(false);
  const [editForm, setEditForm] = useState({ name: "", email: "" });
  const [editError, setEditError] = useState("");
  const [editSuccess, setEditSuccess] = useState("");
  const [saving, setSaving] = useState(false);

  // Password change
  const [showPasswordForm, setShowPasswordForm] = useState(false);
  const [pwForm, setPwForm] = useState({ current_password: "", password: "", password_confirmation: "" });
  const [pwError, setPwError] = useState("");
  const [pwSuccess, setPwSuccess] = useState("");
  const [pwSaving, setPwSaving] = useState(false);

  useEffect(() => {
    fetch("/api/account", {
      headers: authHeaders(),
      credentials: "same-origin",
    })
      .then((res) => {
        if (!res.ok) throw new Error("Failed to load account details");
        return res.json();
      })
      .then((data) => {
        setAccount(data);
        setEditForm({ name: data.name, email: data.email });
      })
      .catch(() => setError("Could not load account details."))
      .finally(() => setLoading(false));
  }, []);

  const saveProfile = async (e) => {
    e.preventDefault();
    setEditError("");
    setEditSuccess("");
    setSaving(true);
    try {
      const res = await fetch("/api/account", {
        method: "PUT",
        headers: { ...authHeaders(), "Content-Type": "application/json" },
        credentials: "same-origin",
        body: JSON.stringify(editForm),
      });
      const data = await res.json();
      if (!res.ok) {
        setEditError(data.message || "Failed to update profile.");
        return;
      }
      setAccount({ ...account, ...data.user });
      setEditing(false);
      setEditSuccess("Profile updated successfully.");
    } catch {
      setEditError("Network error. Please try again.");
    } finally {
      setSaving(false);
    }
  };

  const changePassword = async (e) => {
    e.preventDefault();
    setPwError("");
    setPwSuccess("");
    setPwSaving(true);
    try {
      const res = await fetch("/api/account/password", {
        method: "PUT",
        headers: { ...authHeaders(), "Content-Type": "application/json" },
        credentials: "same-origin",
        body: JSON.stringify(pwForm),
      });
      const data = await res.json();
      if (!res.ok) {
        setPwError(data.message || "Failed to change password.");
        return;
      }
      setPwSuccess("Password changed successfully.");
      setShowPasswordForm(false);
      setPwForm({ current_password: "", password: "", password_confirmation: "" });
    } catch {
      setPwError("Network error. Please try again.");
    } finally {
      setPwSaving(false);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background">
        <p className="text-muted-foreground">Loading account details...</p>
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background p-4">
        <Card className="w-full max-w-md">
          <CardContent className="pt-6 text-center">
            <p className="text-destructive mb-4">{error}</p>
            <Button variant="outline" onClick={onBack}>Go Back</Button>
          </CardContent>
        </Card>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-background p-6 lg:p-8">
      <div className="max-w-2xl mx-auto space-y-6">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold tracking-tight">Account Details</h1>
            <p className="text-muted-foreground">Your account information and status.</p>
          </div>
          <Button variant="outline" onClick={onBack}>Back to Dashboard</Button>
        </div>

        {/* Profile Card */}
        <Card>
          <CardHeader className="flex flex-row items-center justify-between">
            <div>
              <CardTitle>Profile Information</CardTitle>
              <CardDescription>Your personal account details.</CardDescription>
            </div>
            {!editing && (
              <Button variant="outline" size="sm" onClick={() => { setEditing(true); setEditError(""); setEditSuccess(""); }}>
                Edit Profile
              </Button>
            )}
          </CardHeader>
          <CardContent className="space-y-4">
            {editSuccess && (
              <div className="p-3 text-sm text-green-600 bg-green-50 border border-green-200 rounded-md">{editSuccess}</div>
            )}

            {editing ? (
              <form onSubmit={saveProfile} className="space-y-4">
                {editError && (
                  <div className="p-3 text-sm text-destructive bg-destructive/10 border border-destructive/20 rounded-md">{editError}</div>
                )}
                <div className="grid grid-cols-2 gap-4">
                  <div className="space-y-1">
                    <Label>Name</Label>
                    <Input value={editForm.name} onChange={e => setEditForm({ ...editForm, name: e.target.value })} required />
                  </div>
                  <div className="space-y-1">
                    <Label>Email</Label>
                    <Input type="email" value={editForm.email} onChange={e => setEditForm({ ...editForm, email: e.target.value })} required />
                  </div>
                </div>
                <div className="flex gap-2">
                  <Button type="submit" size="sm" disabled={saving}>{saving ? "Saving..." : "Save Changes"}</Button>
                  <Button type="button" variant="outline" size="sm" onClick={() => { setEditing(false); setEditForm({ name: account.name, email: account.email }); }}>Cancel</Button>
                </div>
              </form>
            ) : (
              <>
                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <p className="text-sm font-medium text-muted-foreground">User ID</p>
                    <p className="text-lg font-mono">{account.id}</p>
                  </div>
                  <div>
                    <p className="text-sm font-medium text-muted-foreground">Name</p>
                    <p className="text-lg">{account.name}</p>
                  </div>
                </div>
                <Separator />
                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <p className="text-sm font-medium text-muted-foreground">Email</p>
                    <p className="text-lg">{account.email}</p>
                  </div>
                  <div>
                    <p className="text-sm font-medium text-muted-foreground">Role</p>
                    <Badge variant="secondary" className="capitalize">{account.role}</Badge>
                  </div>
                </div>
                <Separator />
                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <p className="text-sm font-medium text-muted-foreground">Email Verified</p>
                    <Badge variant={account.is_email_verified ? "default" : "outline"}>
                      {account.is_email_verified ? "Verified" : "Not Verified"}
                    </Badge>
                  </div>
                  <div>
                    <p className="text-sm font-medium text-muted-foreground">Onboarding Status</p>
                    <Badge variant="secondary" className="capitalize">{account.onboarding_status}</Badge>
                  </div>
                </div>
                <Separator />
                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <p className="text-sm font-medium text-muted-foreground">Account Created</p>
                    <p className="text-sm">{new Date(account.created_at).toLocaleDateString("en-US", { year: "numeric", month: "long", day: "numeric" })}</p>
                  </div>
                  <div>
                    <p className="text-sm font-medium text-muted-foreground">Last Updated</p>
                    <p className="text-sm">{new Date(account.updated_at).toLocaleDateString("en-US", { year: "numeric", month: "long", day: "numeric" })}</p>
                  </div>
                </div>
              </>
            )}
          </CardContent>
        </Card>

        {/* Password & Security Card */}
        <Card>
          <CardHeader className="flex flex-row items-center justify-between">
            <div>
              <CardTitle>Password & Security</CardTitle>
              <CardDescription>Manage your password.</CardDescription>
            </div>
            {!showPasswordForm && (
              <Button variant="outline" size="sm" onClick={() => { setShowPasswordForm(true); setPwError(""); setPwSuccess(""); }}>
                Change Password
              </Button>
            )}
          </CardHeader>
          <CardContent className="space-y-4">
            {pwSuccess && (
              <div className="p-3 text-sm text-green-600 bg-green-50 border border-green-200 rounded-md">{pwSuccess}</div>
            )}

            {showPasswordForm ? (
              <form onSubmit={changePassword} className="space-y-4">
                {pwError && (
                  <div className="p-3 text-sm text-destructive bg-destructive/10 border border-destructive/20 rounded-md">{pwError}</div>
                )}
                <div className="space-y-1">
                  <Label>Current Password</Label>
                  <Input type="password" value={pwForm.current_password} onChange={e => setPwForm({ ...pwForm, current_password: e.target.value })} required autoComplete="current-password" />
                </div>
                <div className="grid grid-cols-2 gap-4">
                  <div className="space-y-1">
                    <Label>New Password</Label>
                    <Input type="password" value={pwForm.password} onChange={e => setPwForm({ ...pwForm, password: e.target.value })} required minLength={8} autoComplete="new-password" />
                  </div>
                  <div className="space-y-1">
                    <Label>Confirm New Password</Label>
                    <Input type="password" value={pwForm.password_confirmation} onChange={e => setPwForm({ ...pwForm, password_confirmation: e.target.value })} required minLength={8} autoComplete="new-password" />
                  </div>
                </div>
                <div className="flex gap-2">
                  <Button type="submit" size="sm" disabled={pwSaving}>{pwSaving ? "Changing..." : "Change Password"}</Button>
                  <Button type="button" variant="outline" size="sm" onClick={() => { setShowPasswordForm(false); setPwForm({ current_password: "", password: "", password_confirmation: "" }); }}>Cancel</Button>
                </div>
              </form>
            ) : (
              <div className="flex items-center gap-3 p-4 bg-muted/50 rounded-lg">
                <div className="flex-1">
                  <p className="text-sm font-medium">Password</p>
                  <p className="text-sm text-muted-foreground">Stored as a bcrypt hash — never saved in plain text.</p>
                </div>
                <Badge variant="outline">Secured</Badge>
              </div>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
