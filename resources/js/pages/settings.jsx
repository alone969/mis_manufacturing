import { useState, useEffect } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Label } from "@/components/ui/label";
import { Badge } from "@/components/ui/badge";
import { authHeaders } from "@/lib/utils";

export default function Settings({ onBack, user }) {
  const [settings, setSettings] = useState({
    language: "en",
    email_notifications: true,
    shift_reminders: true,
    theme: "system",
  });
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");

  useEffect(() => {
    fetch("/api/account", { headers: authHeaders(), credentials: "same-origin" })
      .then(r => r.json())
      .then(data => {
        if (data.settings) {
          setSettings(prev => ({ ...prev, ...data.settings }));
        }
      })
      .catch(() => setError("Failed to load settings."))
      .finally(() => setLoading(false));
  }, []);

  const saveSettings = async () => {
    setSaving(true);
    setError("");
    setSuccess("");
    try {
      const res = await fetch("/api/account/settings", {
        method: "PUT",
        headers: { ...authHeaders(), "Content-Type": "application/json" },
        credentials: "same-origin",
        body: JSON.stringify({ settings }),
      });
      const data = await res.json();
      if (!res.ok) {
        setError(data.message || "Failed to save settings.");
        return;
      }
      setSettings(data.settings || settings);
      setSuccess("Settings saved successfully.");
    } catch {
      setError("Network error. Please try again.");
    } finally {
      setSaving(false);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background">
        <p className="text-muted-foreground">Loading settings...</p>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-background p-6 lg:p-8">
      <div className="max-w-2xl mx-auto space-y-6">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold tracking-tight">Settings</h1>
            <p className="text-muted-foreground">Configure your preferences.</p>
          </div>
          <Button variant="outline" onClick={onBack}>← Back</Button>
        </div>

        {error && (
          <div className="p-3 text-sm text-destructive bg-destructive/10 border border-destructive/20 rounded-md">{error}</div>
        )}
        {success && (
          <div className="p-3 text-sm text-green-600 bg-green-50 border border-green-200 rounded-md">{success}</div>
        )}

        {/* Appearance */}
        <Card>
          <CardHeader>
            <CardTitle>Appearance</CardTitle>
            <CardDescription>Customize the look and feel.</CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-2">
              <Label>Theme</Label>
              <div className="flex gap-2">
                {["light", "dark", "system"].map(theme => (
                  <Badge
                    key={theme}
                    variant={settings.theme === theme ? "default" : "outline"}
                    className="cursor-pointer px-4 py-1 capitalize"
                    onClick={() => setSettings({ ...settings, theme })}
                  >
                    {theme}
                  </Badge>
                ))}
              </div>
            </div>

            <div className="space-y-2">
              <Label>Language</Label>
              <select
                value={settings.language}
                onChange={e => setSettings({ ...settings, language: e.target.value })}
                className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm"
              >
                <option value="en">English</option>
                <option value="es">Spanish</option>
                <option value="fr">French</option>
                <option value="de">German</option>
                <option value="ar">Arabic</option>
              </select>
            </div>
          </CardContent>
        </Card>

        {/* Notifications */}
        <Card>
          <CardHeader>
            <CardTitle>Notifications</CardTitle>
            <CardDescription>Manage your notification preferences.</CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium">Email Notifications</p>
                <p className="text-xs text-muted-foreground">Receive email notifications for important events.</p>
              </div>
              <button
                onClick={() => setSettings({ ...settings, email_notifications: !settings.email_notifications })}
                className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors ${settings.email_notifications ? "bg-primary" : "bg-muted"}`}
              >
                <span className={`inline-block h-4 w-4 transform rounded-full bg-white transition-transform ${settings.email_notifications ? "translate-x-6" : "translate-x-1"}`} />
              </button>
            </div>

            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium">Shift Reminders</p>
                <p className="text-xs text-muted-foreground">Get reminded before your shift starts.</p>
              </div>
              <button
                onClick={() => setSettings({ ...settings, shift_reminders: !settings.shift_reminders })}
                className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors ${settings.shift_reminders ? "bg-primary" : "bg-muted"}`}
              >
                <span className={`inline-block h-4 w-4 transform rounded-full bg-white transition-transform ${settings.shift_reminders ? "translate-x-6" : "translate-x-1"}`} />
              </button>
            </div>
          </CardContent>
        </Card>

        <div className="flex justify-end">
          <Button onClick={saveSettings} disabled={saving}>
            {saving ? "Saving..." : "Save Settings"}
          </Button>
        </div>
      </div>
    </div>
  );
}
