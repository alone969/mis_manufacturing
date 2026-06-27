# MIS Manufacturing - Performance Optimization Guide

## Server Requirements

- **PHP**: 8.3+ with OPcache enabled
- **MySQL**: 8.0+
- **Memory**: 2GB+ RAM
- **Storage**: SSD recommended

## PHP Configuration (php.ini)

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.revalidate_freq=0
opcache.validate_timestamps=0
opcache.save_comments=1
opcache.jit=1255
opcache.jit_buffer_size=128M

memory_limit=512M
max_execution_time=30
max_input_time=60

# OPcache for CLI (for artisan commands)
opcache.enable_cli=1
```

## Laravel Optimizations

### 1. Cache Configuration
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 2. Database Optimization
- Add indexes on frequently queried columns
- Use eager loading to prevent N+1 queries
- Use `select()` to limit returned columns
- Paginate large result sets

### 3. Queue Workers
- Use Redis or SQS for queue driver in production
- Run multiple queue worker processes
- Use `--max-time` to restart workers periodically

### 4. Caching Strategy
```php
// Cache frequently accessed data
$roles = Cache::remember('roles', 3600, function () {
    return Role::all();
});

// Cache dashboard stats
$stats = Cache::remember("dashboard_{$user->role}", 300, function () use ($user) {
    return $this->getDashboardData($user);
});
```

### 5. Image/Asset Optimization
- Use `Vite` for asset bundling with cache busting
- Enable gzip/brotli compression on the web server
- Use a CDN for static assets

## Database Indexes

Ensure the following indexes exist for optimal performance:

```sql
-- Users
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);

-- Shift Assignments
CREATE INDEX idx_shift_assignments_user_date ON shift_assignments(user_id, date);
CREATE INDEX idx_shift_assignments_date ON shift_assignments(date);

-- Stock Items
CREATE INDEX idx_stock_items_type ON stock_items(type);
CREATE INDEX idx_stock_items_quantity ON stock_items(quantity);

-- Salaries
CREATE INDEX idx_salaries_user_id ON salaries(user_id);
CREATE INDEX idx_salaries_status ON salaries(status);

-- Messages
CREATE INDEX idx_messages_receiver ON messages(receiver_id, is_deleted_by_receiver);
CREATE INDEX idx_messages_sender ON messages(sender_id, is_deleted_by_sender);

-- Activity Logs
CREATE INDEX idx_activity_logs_user ON activity_logs(user_id);
CREATE INDEX idx_activity_logs_action ON activity_logs(action);
CREATE INDEX idx_activity_logs_created ON activity_logs(created_at);
```

## Monitoring

### Queue Monitoring (Horizon)
```bash
# Start Horizon for queue monitoring dashboard
php artisan horizon

# Or run in background
php artisan horizon:daemon
```

### Log Monitoring
```bash
# Watch logs in real-time
tail -f storage/logs/laravel.log

# Check for errors
tail -100 storage/logs/laravel.log | grep -i "error\|exception"
```

### Queue Status
```bash
# Check queue size
php artisan queue:size

# Check failed jobs
php artisan queue:failed
```

## Security Headers

Already configured in Nginx:
- `X-Frame-Options: SAMEORIGIN`
- `X-Content-Type-Options: nosniff`
- `X-XSS-Protection: 1; mode=block`
- `Strict-Transport-Security: max-age=31536000`
