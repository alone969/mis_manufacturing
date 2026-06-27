<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'MIS Manufacturing') }} - Clothing Manufacturing Management System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; background: #f8fafc; color: #1e293b; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }

        /* Navbar */
        .navbar { background: white; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 100; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .navbar .container { display: flex; align-items: center; justify-content: space-between; height: 72px; }
        .navbar-brand { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .navbar-logo { width: 42px; height: 42px; background: linear-gradient(135deg, #2563eb, #1d4ed8); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 18px; }
        .navbar-title { font-size: 20px; font-weight: 700; color: #0f172a; }
        .navbar-subtitle { font-size: 11px; color: #64748b; font-weight: 500; letter-spacing: 0.5px; text-transform: uppercase; }
        .navbar-links { display: flex; align-items: center; gap: 8px; }
        .btn { display: inline-flex; align-items: center; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s; cursor: pointer; border: none; }
        .btn-ghost { color: #475569; background: transparent; }
        .btn-ghost:hover { background: #f1f5f9; color: #1e293b; }
        .btn-primary { color: white; background: #2563eb; }
        .btn-primary:hover { background: #1d4ed8; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(37,99,235,0.3); }
        .btn-white { color: #1e293b; background: white; }
        .btn-white:hover { background: #f8fafc; transform: translateY(-1px); }
        .btn-outline { color: white; background: transparent; border: 2px solid rgba(255,255,255,0.4); }
        .btn-outline:hover { background: rgba(255,255,255,0.1); border-color: white; }

        /* Hero */
        .hero { background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%); color: white; padding: 100px 0 80px; position: relative; overflow: hidden; }
        .hero::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"); opacity: 0.5; }
        .hero-content { position: relative; z-index: 1; }
        .hero .container { display: flex; align-items: center; gap: 60px; }
        .hero-text { flex: 1; }
        .hero-badge { display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); border-radius: 100px; padding: 6px 16px; font-size: 13px; font-weight: 500; margin-bottom: 24px; backdrop-filter: blur(10px); }
        .hero-badge .dot { width: 8px; height: 8px; background: #22c55e; border-radius: 50%; animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        .hero h1 { font-size: 48px; font-weight: 800; line-height: 1.15; margin-bottom: 20px; letter-spacing: -0.02em; }
        .hero h1 span { background: linear-gradient(135deg, #60a5fa, #a78bfa); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .hero p { font-size: 18px; color: #94a3b8; max-width: 520px; margin-bottom: 36px; line-height: 1.7; }
        .hero-actions { display: flex; gap: 12px; flex-wrap: wrap; }
        .hero-stats { flex: 1; display: grid; grid-template-columns: 1fr 1fr; gap: 16px; max-width: 400px; }
        .hero-stat { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 24px; backdrop-filter: blur(10px); }
        .hero-stat-value { font-size: 32px; font-weight: 800; color: white; }
        .hero-stat-label { font-size: 13px; color: #94a3b8; margin-top: 4px; }

        /* Features */
        .features { padding: 80px 0; background: white; }
        .section-header { text-align: center; margin-bottom: 56px; }
        .section-tag { display: inline-block; font-size: 13px; font-weight: 600; color: #2563eb; background: #eff6ff; padding: 4px 12px; border-radius: 100px; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px; }
        .section-title { font-size: 36px; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; margin-bottom: 16px; }
        .section-desc { font-size: 17px; color: #64748b; max-width: 600px; margin: 0 auto; }
        .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .feature-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 32px; transition: all 0.3s; }
        .feature-card:hover { border-color: #cbd5e1; transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.06); }
        .feature-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 20px; }
        .feature-card h3 { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 10px; }
        .feature-card p { font-size: 14px; color: #64748b; line-height: 1.6; }

        /* How It Works */
        .how-it-works { padding: 80px 0; background: #f8fafc; }
        .steps { display: grid; grid-template-columns: repeat(4, 1fr); gap: 32px; margin-top: 56px; }
        .step { text-align: center; position: relative; }
        .step-number { width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; font-size: 22px; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
        .step h3 { font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
        .step p { font-size: 13px; color: #64748b; }

        /* About */
        .about { padding: 80px 0; background: white; }
        .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; }
        .about-content h2 { font-size: 36px; font-weight: 800; color: #0f172a; margin-bottom: 20px; letter-spacing: -0.02em; }
        .about-content p { font-size: 16px; color: #64748b; margin-bottom: 16px; line-height: 1.7; }
        .about-list { list-style: none; margin: 24px 0; }
        .about-list li { display: flex; align-items: center; gap: 12px; padding: 8px 0; font-size: 15px; color: #334155; }
        .about-list li .check { width: 24px; height: 24px; background: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #16a34a; font-size: 14px; flex-shrink: 0; }
        .about-image { background: linear-gradient(135deg, #1e3a5f, #0f172a); border-radius: 20px; padding: 48px; color: white; text-align: center; }
        .about-image .location { font-size: 14px; color: #94a3b8; margin-bottom: 8px; }
        .about-image h3 { font-size: 28px; font-weight: 800; margin-bottom: 16px; }
        .about-image .map-placeholder { background: rgba(255,255,255,0.08); border-radius: 12px; padding: 40px; border: 1px dashed rgba(255,255,255,0.2); }
        .about-image .map-placeholder p { color: #94a3b8; font-size: 14px; }

        /* Roles */
        .roles { padding: 80px 0; background: #f8fafc; }
        .roles-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .role-card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 32px; text-align: center; transition: all 0.3s; }
        .role-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.06); }
        .role-icon { width: 64px; height: 64px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 20px; }
        .role-card h3 { font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 12px; }
        .role-card ul { list-style: none; text-align: left; }
        .role-card li { padding: 6px 0; font-size: 14px; color: #64748b; display: flex; align-items: center; gap: 8px; }
        .role-card li::before { content: '✓'; color: #22c55e; font-weight: 700; font-size: 12px; }

        /* Footer */
        .footer { background: #0f172a; color: #94a3b8; padding: 48px 0 32px; }
        .footer-content { display: flex; justify-content: space-between; align-items: center; }
        .footer-brand { display: flex; align-items: center; gap: 12px; }
        .footer-brand .logo { width: 36px; height: 36px; background: linear-gradient(135deg, #2563eb, #1d4ed8); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 14px; }
        .footer-brand span { color: white; font-weight: 600; }
        .footer-info { text-align: right; font-size: 13px; }
        .footer-info a { color: #60a5fa; text-decoration: none; }

        @media (max-width: 768px) {
            .hero .container { flex-direction: column; text-align: center; }
            .hero h1 { font-size: 32px; }
            .hero p { margin: 0 auto 36px; }
            .hero-actions { justify-content: center; }
            .hero-stats { max-width: 100%; }
            .features-grid, .roles-grid { grid-template-columns: 1fr; }
            .steps { grid-template-columns: repeat(2, 1fr); }
            .about-grid { grid-template-columns: 1fr; }
            .footer-content { flex-direction: column; gap: 16px; text-align: center; }
            .footer-info { text-align: center; }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <a href="/" class="navbar-brand">
                <div class="navbar-logo"><svg viewBox="0 0 100 100" width="24" height="24" xmlns="http://www.w3.org/2000/svg"><path d="M25 15 L35 10 L42 20 L50 17 L58 20 L65 10 L75 15 L85 30 L72 38 L72 82 L28 82 L28 38 L15 30 Z" fill="white" stroke="white" stroke-width="2" stroke-linejoin="round"/><line x1="50" y1="35" x2="50" y2="70" stroke="rgba(255,255,255,0.6)" stroke-width="1.5" stroke-dasharray="4,3"/><circle cx="50" cy="33" r="2.5" fill="white"/></svg></div>
                <div>
                    <div class="navbar-title">MIS Manufacturing</div>
                    <div class="navbar-subtitle">Birendranagar - Surkhet</div>
                </div>
            </a>
            <div class="navbar-links">
                <a href="#features" class="btn btn-ghost">Features</a>
                <a href="#about" class="btn btn-ghost">About</a>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-ghost">Sign In</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary">Get Started</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <div class="container hero-content">
            <div class="hero-text">
                <div class="hero-badge">
                    <span class="dot"></span>
                    Now serving Birendranagar, Surkhet
                </div>
                <h1>Manufacturing Management <span>System</span> for Clothing Industry of Birendranagar - Surkhet</h1>
                <p>Streamline your clothing manufacturing operations with centralized employee management, shift scheduling, attendance tracking, stock control, and salary processing — all in one powerful platform.</p>
                <div class="hero-actions">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary" style="padding: 14px 28px; font-size: 16px;">Go to Dashboard →</a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-primary" style="padding: 14px 28px; font-size: 16px;">Start Free Trial →</a>
                        <a href="{{ route('login') }}" class="btn btn-outline" style="padding: 14px 28px; font-size: 16px;">Sign In</a>
                    @endauth
                </div>
            </div>
            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="hero-stat-value">50+</div>
                    <div class="hero-stat-label">Employees Managed</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-value">24/7</div>
                    <div class="hero-stat-label">System Uptime</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-value">3</div>
                    <div class="hero-stat-label">Shift Types</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-value">100%</div>
                    <div class="hero-stat-label">Digital Tracking</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-header">
                <div class="section-tag">Features</div>
                <h2 class="section-title">Everything You Need to Manage</h2>
                <p class="section-desc">A complete management system designed specifically for clothing manufacturing companies.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon" style="background: #eff6ff; color: #2563eb;">👥</div>
                    <h3>Employee Management</h3>
                    <p>Manage employee profiles, roles, and permissions. Track onboarding status and manage team hierarchies.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: #f0fdf4; color: #16a34a;">⏰</div>
                    <h3>Shift Scheduling</h3>
                    <p>Create and manage shifts (Morning, Afternoon, Night). Assign employees and view weekly/monthly schedules.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: #fef3c7; color: #d97706;">📋</div>
                    <h3>Attendance Tracking</h3>
                    <p>One-click clock in/out with automatic late detection. View attendance history and generate reports.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: #fce7f3; color: #db2777;">📦</div>
                    <h3>Stock Management</h3>
                    <p>Track raw materials and finished goods. Get low stock alerts and manage inventory levels efficiently.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: #f3e8ff; color: #9333ea;">💰</div>
                    <h3>Salary Processing</h3>
                    <p>Process and track employee salaries. Manage payment statuses and maintain salary history.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: #ecfdf5; color: #059669;">💬</div>
                    <h3>Internal Messaging</h3>
                    <p>Send messages between employees, managers, and admins. Keep communication centralized and organized.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: #fff7ed; color: #ea580c;">🔔</div>
                    <h3>Notifications</h3>
                    <p>Get notified about shift assignments, salary processing, and important system announcements.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: #eff6ff; color: #2563eb;">📊</div>
                    <h3>Activity Logs</h3>
                    <p>Monitor all system activities with detailed logs. Track logins, CRUD operations, and attendance events.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: #f0fdf4; color: #16a34a;">🔍</div>
                    <h3>Global Search</h3>
                    <p>Quickly search across employees, shifts, stock items, and more from anywhere in the system.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works">
        <div class="container">
            <div class="section-header">
                <div class="section-tag">How It Works</div>
                <h2 class="section-title">Get Started in 4 Simple Steps</h2>
                <p class="section-desc">Set up your manufacturing management system in minutes.</p>
            </div>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Create Account</h3>
                    <p>Register your company and create the admin account</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Add Employees</h3>
                    <p>Add team members and assign roles (Employee, Manager, Admin)</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Setup Shifts</h3>
                    <p>Create shift schedules and assign employees to shifts</p>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <h3>Start Managing</h3>
                    <p>Track attendance, manage stock, and process salaries</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About / Location -->
    <section class="about" id="about">
        <div class="container">
            <div class="about-grid">
                <div class="about-content">
                    <div class="section-tag">About Us</div>
                    <h2>Clothing Manufacturing Solutions for Birendranagar</h2>
                    <p>We provide a comprehensive Management Information System (MIS) tailored for clothing manufacturing companies in Birendranagar, Surkhet, Nepal. Our platform digitizes manual administrative processes, bringing efficiency and transparency to your operations.</p>
                    <p>From managing employee shifts to tracking inventory and processing payroll — everything you need in one place.</p>
                    <ul class="about-list">
                        <li><span class="check">✓</span> Role-based access control (Admin, Manager, Employee)</li>
                        <li><span class="check">✓</span> Real-time attendance tracking with late detection</li>
                        <li><span class="check">✓</span> Automated shift scheduling and assignment</li>
                        <li><span class="check">✓</span> Low stock alerts and inventory management</li>
                        <li><span class="check">✓</span> Complete audit trail with activity logs</li>
                        <li><span class="check">✓</span> RESTful API ready for mobile applications</li>
                    </ul>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary" style="margin-top: 12px;">Go to Dashboard →</a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-primary" style="margin-top: 12px;">Get Started Free →</a>
                    @endauth
                </div>
                <div class="about-image">
                    <div class="location">📍 Location</div>
                    <h3>Birendranagar, Surkhet</h3>
                    <p style="color: #94a3b8; margin-bottom: 24px;">Karnali Province, Nepal</p>
                    <div class="map-placeholder">
                        <p>🗺️ Clothing Manufacturing Hub</p>
                        <p style="margin-top: 8px;">Birendranagar Industrial Area<br>Surkhet, Nepal</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Roles -->
    <section class="roles">
        <div class="container">
            <div class="section-header">
                <div class="section-tag">User Roles</div>
                <h2 class="section-title">Tailored for Every Team Member</h2>
                <p class="section-desc">Three distinct roles with specific permissions for efficient operations.</p>
            </div>
            <div class="roles-grid">
                <div class="role-card">
                    <div class="role-icon" style="background: #eff6ff; color: #2563eb;">🛡️</div>
                    <h3>Admin</h3>
                    <ul>
                        <li>Manage all employees</li>
                        <li>Full shift management</li>
                        <li>Stock & salary management</li>
                        <li>View activity & device logs</li>
                        <li>System configuration</li>
                    </ul>
                </div>
                <div class="role-card">
                    <div class="role-icon" style="background: #f0fdf4; color: #16a34a;">📋</div>
                    <h3>Manager</h3>
                    <ul>
                        <li>View team members</li>
                        <li>Create & assign shifts</li>
                        <li>View team attendance</li>
                        <li>View stock levels</li>
                        <li>Send messages</li>
                    </ul>
                </div>
                <div class="role-card">
                    <div class="role-icon" style="background: #fef3c7; color: #d97706;">👤</div>
                    <h3>Employee</h3>
                    <ul>
                        <li>Clock in / Clock out</li>
                        <li>View own attendance</li>
                        <li>View assigned shifts</li>
                        <li>View stock & salary</li>
                        <li>Send & receive messages</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <div class="logo"><svg viewBox="0 0 100 100" width="20" height="20" xmlns="http://www.w3.org/2000/svg"><path d="M25 15 L35 10 L42 20 L50 17 L58 20 L65 10 L75 15 L85 30 L72 38 L72 82 L28 82 L28 38 L15 30 Z" fill="white" stroke="white" stroke-width="2" stroke-linejoin="round"/><line x1="50" y1="35" x2="50" y2="70" stroke="rgba(255,255,255,0.6)" stroke-width="1.5" stroke-dasharray="4,3"/><circle cx="50" cy="33" r="2.5" fill="white"/></svg></div>
                    <span>MIS Manufacturing</span>
                </div>
                <div class="footer-info">
                    <p>Birendranagar, Surkhet, Nepal</p>
                    <p style="margin-top: 4px;">© {{ date('Y') }} MIS Manufacturing. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
