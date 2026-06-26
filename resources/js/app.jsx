import { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import Login from '@/pages/login';
import Register from '@/pages/register';
import ForgotPassword from '@/pages/forgot-password';
import ResetPassword from '@/pages/reset-password';
import Dashboard from '@/pages/dashboard';
import Account from '@/pages/account';
import Shifts from '@/pages/shifts';
import Stock from '@/pages/stock';
import Salaries from '@/pages/salaries';
import Employees from '@/pages/employees';
import ActivityLog from '@/pages/activity-log';
import Messages from '@/pages/messages';
import DeviceLogs from '@/pages/device-logs';
import Notifications from '@/pages/notifications';
import Settings from '@/pages/settings';
import { authHeaders } from '@/lib/utils';

function App() {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);
    const [view, setView] = useState('dashboard');

    useEffect(() => {
        const savedUser = localStorage.getItem('user');
        if (savedUser) {
            fetch('/api/user', {
                headers: authHeaders(),
                credentials: 'same-origin',
            })
                .then((res) => {
                    if (res.ok) return res.json();
                    throw new Error('Session expired');
                })
                .then((data) => setUser(data))
                .catch(() => {
                    localStorage.removeItem('user');
                    setUser(null);
                })
                .finally(() => setLoading(false));
        } else {
            setLoading(false);
        }
    }, []);

    const handleLogin = (userData) => {
        setUser(userData);
        setView('dashboard');
    };

    const handleLogout = async () => {
        try {
            await fetch('/api/logout', {
                method: 'POST',
                headers: authHeaders(),
                credentials: 'same-origin',
            });
        } catch (err) {
            // Ignore network errors on logout
        }
        localStorage.removeItem('user');
        setUser(null);
        setView('dashboard');
    };

    const navigateTo = (page) => setView(page);
    const backToDashboard = () => setView('dashboard');

    if (loading) {
        return (
            <div className="min-h-screen flex items-center justify-center bg-background">
                <p className="text-muted-foreground">Loading...</p>
            </div>
        );
    }

    if (!user) {
        if (view === 'register') {
            return <Register onRegister={handleLogin} onSwitchToLogin={() => setView('login')} />;
        }
        if (view === 'forgot-password') {
            return <ForgotPassword onSwitchToLogin={() => setView('login')} />;
        }
        if (view === 'reset-password') {
            return <ResetPassword onSwitchToLogin={() => setView('login')} />;
        }
        return <Login onLogin={handleLogin} onSwitchToRegister={() => setView('register')} onSwitchToForgotPassword={() => setView('forgot-password')} onSwitchToResetPassword={() => setView('reset-password')} />;
    }

    switch (view) {
        case 'account':
            return <Account onBack={backToDashboard} />;
        case 'shifts':
            return <Shifts onBack={backToDashboard} user={user} />;
        case 'stock':
            return <Stock onBack={backToDashboard} user={user} />;
        case 'salaries':
            return <Salaries onBack={backToDashboard} />;
        case 'employees':
            return <Employees onBack={backToDashboard} />;
        case 'activity':
            return <ActivityLog onBack={backToDashboard} />;
        case 'messages':
            return <Messages onBack={backToDashboard} />;
        case 'device-logs':
            return <DeviceLogs onBack={backToDashboard} />;
        case 'notifications':
            return <Notifications onBack={backToDashboard} />;
        case 'settings':
            return <Settings onBack={backToDashboard} user={user} />;
        default:
            return <Dashboard user={user} onLogout={handleLogout} onViewAccount={() => navigateTo('account')} onNavigate={navigateTo} />;
    }
}

const root = createRoot(document.getElementById('app'));
root.render(<App />);
