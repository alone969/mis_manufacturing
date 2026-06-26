import { useState, useEffect } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Badge } from "@/components/ui/badge";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Separator } from "@/components/ui/separator";
import { authHeaders } from "@/lib/utils";

export default function Messages({ onBack }) {
  const [messages, setMessages] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [box, setBox] = useState("inbox");
  const [showCompose, setShowCompose] = useState(false);
  const [users, setUsers] = useState([]);
  const [unreadCount, setUnreadCount] = useState(0);
  const [form, setForm] = useState({ receiver_id: "", subject: "", body: "" });
  const [selectedMessage, setSelectedMessage] = useState(null);

  const loadMessages = (mailbox) => {
    setLoading(true);
    setBox(mailbox);
    setSelectedMessage(null);
    fetch(`/api/messages?box=${mailbox}`, { headers: authHeaders(), credentials: "same-origin" })
      .then(r => r.json())
      .then(d => setMessages(d.data || []))
      .catch(() => setError("Failed to load messages."))
      .finally(() => setLoading(false));
  };

  useEffect(() => {
    loadMessages("inbox");
    fetch("/api/users", { headers: authHeaders(), credentials: "same-origin" })
      .then(r => r.json())
      .then(d => setUsers(d))
      .catch(() => {});
    fetch("/api/messages/unread-count", { headers: authHeaders(), credentials: "same-origin" })
      .then(r => r.json())
      .then(d => setUnreadCount(d.unread_count || 0))
      .catch(() => {});
  }, []);

  const sendMessage = async (e) => {
    e.preventDefault();
    setError("");
    const res = await fetch("/api/messages", {
      method: "POST",
      headers: { ...authHeaders(), "Content-Type": "application/json" },
      credentials: "same-origin",
      body: JSON.stringify(form),
    });
    if (!res.ok) {
      const data = await res.json();
      setError(data.message || "Failed to send message.");
      return;
    }
    setForm({ receiver_id: "", subject: "", body: "" });
    setShowCompose(false);
    if (box === "sent") loadMessages("sent");
  };

  const markAsRead = async (message) => {
    if (message.read_at) return;
    await fetch(`/api/messages/${message.id}/read`, {
      method: "PUT",
      headers: authHeaders(),
      credentials: "same-origin",
    });
    setMessages(messages.map(m => m.id === message.id ? { ...m, read_at: new Date().toISOString() } : m));
    setUnreadCount(Math.max(0, unreadCount - 1));
  };

  const openMessage = (message) => {
    setSelectedMessage(message);
    markAsRead(message);
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background">
        <p className="text-muted-foreground">Loading messages...</p>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-background p-6 lg:p-8">
      <div className="max-w-5xl mx-auto space-y-8">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold tracking-tight">Messages</h1>
            <p className="text-muted-foreground">Send and receive internal messages.</p>
          </div>
          <div className="flex items-center gap-2">
            <Button size="sm" onClick={() => setShowCompose(!showCompose)}>
              {showCompose ? "Cancel" : "+ Compose"}
            </Button>
            <Button variant="outline" onClick={onBack}>← Back</Button>
          </div>
        </div>

        {error && (
          <div className="p-3 text-sm text-destructive bg-destructive/10 border border-destructive/20 rounded-md">{error}</div>
        )}

        {/* Compose Form */}
        {showCompose && (
          <Card>
            <CardHeader>
              <CardTitle>Compose Message</CardTitle>
            </CardHeader>
            <CardContent>
              <form onSubmit={sendMessage} className="space-y-4">
                <div className="space-y-1">
                  <Label>To</Label>
                  <select
                    value={form.receiver_id}
                    onChange={e => setForm({ ...form, receiver_id: e.target.value })}
                    className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm"
                    required
                  >
                    <option value="">Select recipient...</option>
                    {users.map(u => <option key={u.id} value={u.id}>{u.name} ({u.email})</option>)}
                  </select>
                </div>
                <div className="space-y-1">
                  <Label>Subject</Label>
                  <Input value={form.subject} onChange={e => setForm({ ...form, subject: e.target.value })} required placeholder="Message subject" />
                </div>
                <div className="space-y-1">
                  <Label>Body</Label>
                  <textarea
                    value={form.body}
                    onChange={e => setForm({ ...form, body: e.target.value })}
                    required
                    rows={4}
                    placeholder="Type your message..."
                    className="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                  />
                </div>
                <Button type="submit" size="sm">Send Message</Button>
              </form>
            </CardContent>
          </Card>
        )}

        {/* Selected Message View */}
        {selectedMessage && (
          <Card>
            <CardHeader className="flex flex-row items-center justify-between">
              <div>
                <CardTitle>{selectedMessage.subject}</CardTitle>
                <CardDescription>
                  From: {selectedMessage.sender?.name} · {new Date(selectedMessage.created_at).toLocaleString()}
                </CardDescription>
              </div>
              <Button variant="outline" size="sm" onClick={() => setSelectedMessage(null)}>Close</Button>
            </CardHeader>
            <CardContent>
              <p className="whitespace-pre-wrap text-sm">{selectedMessage.body}</p>
            </CardContent>
          </Card>
        )}

        {/* Mailbox Tabs */}
        <div className="flex gap-2">
          <Badge
            variant={box === "inbox" ? "default" : "outline"}
            className="cursor-pointer px-4 py-1"
            onClick={() => loadMessages("inbox")}
          >
            Inbox {unreadCount > 0 && `(${unreadCount})`}
          </Badge>
          <Badge
            variant={box === "sent" ? "default" : "outline"}
            className="cursor-pointer px-4 py-1"
            onClick={() => loadMessages("sent")}
          >
            Sent
          </Badge>
        </div>

        {/* Messages Table */}
        <Card>
          <CardContent className="pt-6">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>{box === "inbox" ? "From" : "To"}</TableHead>
                  <TableHead>Subject</TableHead>
                  <TableHead>Date</TableHead>
                  <TableHead>Status</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {messages.map(msg => (
                  <TableRow
                    key={msg.id}
                    className={`cursor-pointer hover:bg-muted/50 ${!msg.read_at && box === "inbox" ? "font-semibold" : ""}`}
                    onClick={() => openMessage(msg)}
                  >
                    <TableCell className="font-medium">
                      {box === "inbox" ? msg.sender?.name : msg.receiver?.name}
                    </TableCell>
                    <TableCell>{msg.subject}</TableCell>
                    <TableCell className="text-sm text-muted-foreground">{new Date(msg.created_at).toLocaleDateString()}</TableCell>
                    <TableCell>
                      {box === "inbox" && !msg.read_at ? (
                        <Badge variant="default">Unread</Badge>
                      ) : (
                        <Badge variant="outline">Read</Badge>
                      )}
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
            {messages.length === 0 && (
              <p className="text-sm text-muted-foreground text-center py-4">
                {box === "inbox" ? "No messages in your inbox." : "No sent messages."}
              </p>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
