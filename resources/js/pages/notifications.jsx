import { useState, useEffect } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { authHeaders } from "@/lib/utils";

export default function Notifications({ onBack }) {
  const [notifications, setNotifications] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [page, setPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);
  const [unreadCount, setUnreadCount] = useState(0);

  const loadNotifications = (p) => {
    setLoading(true);
    fetch(`/api/notifications?page=${p}`, { headers: authHeaders(), credentials: "same-origin" })
      .then(r => r.json())
      .then(d => {
        setNotifications(d.data || []);
        setLastPage(d.last_page || 1);
      })
      .catch(() => setError("Failed to load notifications."))
      .finally(() => setLoading(false));
  };

  useEffect(() => {
    loadNotifications(page);
    fetch("/api/notifications/unread-count", { headers: authHeaders(), credentials: "same-origin" })
      .then(r => r.json())
      .then(d => setUnreadCount(d.unread_count || 0))
      .catch(() => {});
  }, [page]);

  const markAsRead = async (notification) => {
    if (notification.is_read) return;
    await fetch(`/api/notifications/${notification.id}/read`, {
      method: "PUT",
      headers: authHeaders(),
      credentials: "same-origin",
    });
    setNotifications(notifications.map(n => n.id === notification.id ? { ...n, is_read: true, read_at: new Date().toISOString() } : n));
    setUnreadCount(Math.max(0, unreadCount - 1));
  };

  const markAllAsRead = async () => {
    await fetch("/api/notifications/read-all", {
      method: "PUT",
      headers: authHeaders(),
      credentials: "same-origin",
    });
    setNotifications(notifications.map(n => ({ ...n, is_read: true, read_at: new Date().toISOString() })));
    setUnreadCount(0);
  };

  const getTypeIcon = (type) => {
    switch (type) {
      case "shift_change": return "📅";
      case "salary_processed": return "💰";
      case "message": return "✉️";
      case "system": return "⚙️";
      default: return "🔔";
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background">
        <p className="text-muted-foreground">Loading notifications...</p>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-background p-6 lg:p-8">
      <div className="max-w-3xl mx-auto space-y-8">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold tracking-tight">Notifications</h1>
            <p className="text-muted-foreground">
              {unreadCount > 0 ? `You have ${unreadCount} unread notification(s).` : "All caught up!"}
            </p>
          </div>
          <div className="flex items-center gap-2">
            {unreadCount > 0 && (
              <Button variant="outline" size="sm" onClick={markAllAsRead}>Mark All Read</Button>
            )}
            <Button variant="outline" onClick={onBack}>← Back</Button>
          </div>
        </div>

        {error && (
          <div className="p-3 text-sm text-destructive bg-destructive/10 border border-destructive/20 rounded-md">{error}</div>
        )}

        <div className="space-y-3">
          {notifications.map(notification => (
            <Card
              key={notification.id}
              className={`cursor-pointer transition-colors hover:bg-muted/50 ${!notification.is_read ? "border-l-4 border-l-primary" : ""}`}
              onClick={() => markAsRead(notification)}
            >
              <CardContent className="py-4">
                <div className="flex items-start gap-3">
                  <span className="text-lg mt-0.5">{getTypeIcon(notification.type)}</span>
                  <div className="flex-1">
                    <div className="flex items-center gap-2">
                      <p className="text-sm font-semibold">{notification.title}</p>
                      {!notification.is_read && <Badge variant="default" className="text-xs">New</Badge>}
                    </div>
                    {notification.body && (
                      <p className="text-sm text-muted-foreground mt-1">{notification.body}</p>
                    )}
                    <p className="text-xs text-muted-foreground mt-2">
                      {new Date(notification.created_at).toLocaleString()}
                    </p>
                  </div>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>

        {notifications.length === 0 && (
          <Card>
            <CardContent className="py-8 text-center">
              <p className="text-muted-foreground">No notifications yet.</p>
            </CardContent>
          </Card>
        )}

        {/* Pagination */}
        {lastPage > 1 && (
          <div className="flex items-center justify-between">
            <Button variant="outline" size="sm" disabled={page <= 1} onClick={() => setPage(page - 1)}>Previous</Button>
            <span className="text-sm text-muted-foreground">Page {page} of {lastPage}</span>
            <Button variant="outline" size="sm" disabled={page >= lastPage} onClick={() => setPage(page + 1)}>Next</Button>
          </div>
        )}
      </div>
    </div>
  );
}
