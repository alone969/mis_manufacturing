import { useState, useEffect } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Separator } from "@/components/ui/separator";
import { Button } from "@/components/ui/button";
import { authHeaders } from "@/lib/utils";

export default function Account({ onBack }) {
  const [account, setAccount] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    fetch("/api/account", {
      headers: authHeaders(),
      credentials: "same-origin",
    })
      .then((res) => {
        if (!res.ok) throw new Error("Failed to load account details");
        return res.json();
      })
      .then((data) => setAccount(data))
      .catch(() => setError("Could not load account details."))
      .finally(() => setLoading(false));
  }, []);

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

        <Card>
          <CardHeader>
            <CardTitle>Profile Information</CardTitle>
            <CardDescription>Your personal account details stored in the database.</CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
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
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Password & Security</CardTitle>
            <CardDescription>Your password is securely hashed and stored in the database.</CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="flex items-center gap-3 p-4 bg-muted/50 rounded-lg">
              <div className="flex-1">
                <p className="text-sm font-medium">Password</p>
                <p className="text-sm text-muted-foreground">Stored as a bcrypt hash — never saved in plain text.</p>
              </div>
              <Badge variant="outline">Secured</Badge>
            </div>
            <p className="text-xs text-muted-foreground">
              For security, passwords are hashed using Laravel's built-in <code className="bg-muted px-1 py-0.5 rounded">Hash::make()</code> 
              (bcrypt) before being stored. The plain-text password is never saved or recoverable.
            </p>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
