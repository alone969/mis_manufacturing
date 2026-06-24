import { useState } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Input } from "@/components/ui/input";
import { Separator } from "@/components/ui/separator";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Button } from "@/components/ui/button";

const stats = [
  { title: "Total Revenue", value: "$45,231.89", change: "+20.1%", trend: "up" },
  { title: "Subscriptions", value: "+2,350", change: "+180.1%", trend: "up" },
  { title: "Active Users", value: "+12,234", change: "+19%", trend: "up" },
  { title: "Bounce Rate", value: "21.3%", change: "-4.5%", trend: "down" },
];

const recentOrders = [
  { id: "ORD-7352", customer: "Olivia Martin", email: "olivia.martin@email.com", status: "Completed", amount: "$316.00" },
  { id: "ORD-7353", customer: "Jackson Lee", email: "jackson.lee@email.com", status: "Processing", amount: "$242.00" },
  { id: "ORD-7354", customer: "Isabella Nguyen", email: "isabella.nguyen@email.com", status: "Completed", amount: "$837.00" },
  { id: "ORD-7355", customer: "William Kim", email: "will@email.com", status: "Pending", amount: "$721.00" },
  { id: "ORD-7356", customer: "Sofia Davis", email: "sofia.davis@email.com", status: "Completed", amount: "$543.00" },
];

const recentActivity = [
  { user: "OL", name: "Olivia Martin", action: "created a new order", time: "2 minutes ago" },
  { user: "JL", name: "Jackson Lee", action: "updated their profile", time: "5 minutes ago" },
  { user: "IN", name: "Isabella Nguyen", action: "completed payment", time: "12 minutes ago" },
  { user: "WK", name: "William Kim", action: "submitted a support ticket", time: "1 hour ago" },
  { user: "SD", name: "Sofia Davis", action: "placed a new order", time: "2 hours ago" },
];

function getStatusVariant(status) {
  switch (status) {
    case "Completed":
      return "default";
    case "Processing":
      return "secondary";
    case "Pending":
      return "outline";
    default:
      return "default";
  }
}

export default function Dashboard() {
  const [search, setSearch] = useState("");

  const filteredOrders = recentOrders.filter(
    (order) =>
      order.customer.toLowerCase().includes(search.toLowerCase()) ||
      order.id.toLowerCase().includes(search.toLowerCase())
  );

  return (
    <div className="min-h-screen bg-background p-6 lg:p-8">
      <div className="max-w-7xl mx-auto space-y-8">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold tracking-tight">Dashboard</h1>
            <p className="text-muted-foreground">Welcome back! Here's an overview of your store.</p>
          </div>
          <div className="flex items-center gap-4">
            <Input
              placeholder="Search orders..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-64"
            />
            <Button>Add Product</Button>
          </div>
        </div>

        {/* Stats Cards */}
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
          {stats.map((stat) => (
            <Card key={stat.title}>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">{stat.title}</CardTitle>
                <Badge variant={stat.trend === "up" ? "default" : "secondary"} className="text-xs">
                  {stat.change}
                </Badge>
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{stat.value}</div>
              </CardContent>
            </Card>
          ))}
        </div>

        <div className="grid gap-6 lg:grid-cols-7">
          {/* Recent Orders Table */}
          <Card className="lg:col-span-4">
            <CardHeader>
              <CardTitle>Recent Orders</CardTitle>
              <CardDescription>You made {recentOrders.length} orders this month.</CardDescription>
            </CardHeader>
            <CardContent>
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Order</TableHead>
                    <TableHead>Customer</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead className="text-right">Amount</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {filteredOrders.map((order) => (
                    <TableRow key={order.id}>
                      <TableCell className="font-medium">{order.id}</TableCell>
                      <TableCell>{order.customer}</TableCell>
                      <TableCell>
                        <Badge variant={getStatusVariant(order.status)}>{order.status}</Badge>
                      </TableCell>
                      <TableCell className="text-right">{order.amount}</TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </CardContent>
          </Card>

          {/* Recent Activity */}
          <Card className="lg:col-span-3">
            <CardHeader>
              <CardTitle>Recent Activity</CardTitle>
              <CardDescription>Latest actions from your team.</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-6">
                {recentActivity.map((activity, i) => (
                  <div key={i} className="flex items-center gap-4">
                    <Avatar className="h-9 w-9">
                      <AvatarFallback>{activity.user}</AvatarFallback>
                    </Avatar>
                    <div className="flex-1 space-y-1">
                      <p className="text-sm font-medium leading-none">
                        {activity.name}{" "}
                        <span className="text-muted-foreground font-normal">{activity.action}</span>
                      </p>
                      <p className="text-sm text-muted-foreground">{activity.time}</p>
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>

        <Separator />

        {/* Footer */}
        <div className="flex items-center justify-between text-sm text-muted-foreground">
          <p>Built with shadcn/ui + React + Laravel</p>
          <Button variant="ghost" size="sm">View All Orders →</Button>
        </div>
      </div>
    </div>
  );
}
