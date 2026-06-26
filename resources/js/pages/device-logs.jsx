import { useState, useEffect } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { authHeaders } from "@/lib/utils";

export default function DeviceLogs({ onBack }) {
  const [devices, setDevices] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    fetch("/api/device-logs", { headers: authHeaders(), credentials: "same-origin" })
      .then(r => r.json())
      .then(d => setDevices(d.data || d || []))
      .catch(() => setError("Failed to load device logs."))
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background">
        <p className="text-muted-foreground">Loading device logs...</p>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-background p-6 lg:p-8">
      <div className="max-w-5xl mx-auto space-y-8">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold tracking-tight">Device Logs</h1>
            <p className="text-muted-foreground">Devices you have logged in from.</p>
          </div>
          <Button variant="outline" onClick={onBack}>← Back</Button>
        </div>

        {error && (
          <div className="p-3 text-sm text-destructive bg-destructive/10 border border-destructive/20 rounded-md">{error}</div>
        )}

        <Card>
          <CardHeader>
            <CardTitle>Your Devices</CardTitle>
            <CardDescription>All devices used to access your account.</CardDescription>
          </CardHeader>
          <CardContent>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Device</TableHead>
                  <TableHead>IP Address</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead>Last Login</TableHead>
                  <TableHead>Last Activity</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {devices.map(device => (
                  <TableRow key={device.id}>
                    <TableCell className="font-medium">
                      {device.device_name || "Unknown Device"}
                      <p className="text-xs text-muted-foreground max-w-xs truncate">{device.user_agent}</p>
                    </TableCell>
                    <TableCell className="font-mono text-sm">{device.ip_address || "—"}</TableCell>
                    <TableCell>
                      {device.is_current_device ? (
                        <Badge variant="default">Current</Badge>
                      ) : (
                        <Badge variant="outline">Other</Badge>
                      )}
                    </TableCell>
                    <TableCell className="text-sm text-muted-foreground">
                      {device.last_login_at ? new Date(device.last_login_at).toLocaleString() : "—"}
                    </TableCell>
                    <TableCell className="text-sm text-muted-foreground">
                      {device.last_activity_at ? new Date(device.last_activity_at).toLocaleString() : "—"}
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
            {devices.length === 0 && (
              <p className="text-sm text-muted-foreground text-center py-4">No device logs found.</p>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
