<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Money Manager') | {{ config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
        }

        body {
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
        }

        /* ===== Sidebar ===== */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #0f172a, #1e293b);
            color: #cbd5e1;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: transform 0.3s;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 22px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-brand .logo {
            width: 42px;
            height: 42px;
            background: #2563eb;
            color: #fff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .sidebar-brand .brand-text {
            color: #fff;
            font-size: 17px;
            font-weight: 700;
        }

        .sidebar-brand .brand-sub {
            color: #64748b;
            font-size: 12px;
        }

        .nav {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
        }

        .nav-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            padding: 12px 12px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 12px;
            margin: 2px 0;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            transition: 0.2s;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
        }

        .nav-item.active {
            background: #2563eb;
            color: #fff;
        }

        .nav-item .icon {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .user-box {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            background: #64748b;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            flex-shrink: 0;
        }

        .user-name {
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-email {
            color: #64748b;
            font-size: 12px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ===== Main Content ===== */
        .main {
            margin-left: 260px;
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: #fff;
            padding: 0 28px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar .page-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .topbar-link {
            color: #64748b;
            text-decoration: none;
            font-size: 15px;
            position: relative;
            display: flex;
            align-items: center;
        }

        .topbar-link:hover {
            color: #2563eb;
        }

        .badge-count {
            position: absolute;
            top: -6px;
            right: -10px;
            background: #ef4444;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            border-radius: 50%;
            min-width: 17px;
            height: 17px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
        }

        .hamburger {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #334155;
        }

        .content {
            padding: 28px;
            flex: 1;
        }

        /* ===== Cards ===== */
        .card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            padding: 24px;
            margin-bottom: 24px;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 17px;
            font-weight: 700;
            color: #0f172a;
        }

        .grid {
            display: grid;
            gap: 20px;
        }

        .grid-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-4 { grid-template-columns: repeat(4, 1fr); }

        /* ===== Stat Cards ===== */
        .stat-card {
            background: #fff;
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .stat-icon.blue { background: #dbeafe; color: #2563eb; }
        .stat-icon.green { background: #dcfce7; color: #16a34a; }
        .stat-icon.red { background: #fee2e2; color: #dc2626; }
        .stat-icon.purple { background: #ede9fe; color: #7c3aed; }
        .stat-icon.amber { background: #fef3c7; color: #d97706; }
        .stat-icon.teal { background: #ccfbf1; color: #0d9488; }

        .stat-label {
            color: #64748b;
            font-size: 13px;
            font-weight: 500;
        }

        .stat-value {
            color: #0f172a;
            font-size: 22px;
            font-weight: 700;
            margin-top: 3px;
        }

        /* ===== Buttons ===== */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 10px;
            border: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: 0.2s;
        }

        .btn-primary { background: #2563eb; color: #fff; }
        .btn-primary:hover { background: #1d4ed8; }

        .btn-success { background: #16a34a; color: #fff; }
        .btn-success:hover { background: #15803d; }

        .btn-danger { background: #dc2626; color: #fff; }
        .btn-danger:hover { background: #b91c1c; }

        .btn-secondary { background: #e2e8f0; color: #334155; }
        .btn-secondary:hover { background: #cbd5e1; }

        .btn-sm { padding: 7px 12px; font-size: 13px; }

        /* ===== Tables ===== */
        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            text-align: left;
            padding: 12px 14px;
            background: #f8fafc;
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        table td {
            padding: 14px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 14px;
        }

        table tr:last-child td {
            border-bottom: none;
        }

        table tr:hover td {
            background: #f8fafc;
        }

        .td-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-green { background: #dcfce7; color: #16a34a; }
        .badge-red { background: #fee2e2; color: #dc2626; }
        .badge-blue { background: #dbeafe; color: #2563eb; }
        .badge-gray { background: #f1f5f9; color: #64748b; }
        .badge-purple { background: #ede9fe; color: #7c3aed; }
        .badge-amber { background: #fef3c7; color: #d97706; }

        .amount-income { color: #16a34a; font-weight: 600; }
        .amount-expense { color: #dc2626; font-weight: 600; }

        /* ===== Forms ===== */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-size: 14px;
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            height: 44px;
            padding: 0 14px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            transition: 0.2s;
            background: #fff;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        textarea.form-control {
            height: auto;
            padding: 12px 14px;
            resize: vertical;
        }

        select.form-control {
            cursor: pointer;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .invalid-feedback {
            color: #dc2626;
            font-size: 13px;
            margin-top: 6px;
        }

        /* ===== Alerts ===== */
        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* ===== Category color dot ===== */
        .color-dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }

        /* ===== Progress Bar ===== */
        .progress {
            background: #e2e8f0;
            border-radius: 20px;
            height: 8px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            border-radius: 20px;
            background: #2563eb;
            transition: width 0.4s;
        }

        .progress-bar.green { background: #16a34a; }
        .progress-bar.red { background: #dc2626; }
        .progress-bar.amber { background: #d97706; }

        /* ===== Empty state ===== */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #94a3b8;
        }

        .empty-state .icon {
            font-size: 48px;
            margin-bottom: 12px;
        }

        .empty-state p {
            font-size: 15px;
            margin-bottom: 18px;
        }

        /* ===== Pagination ===== */
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }

        .pagination a, .pagination span {
            padding: 8px 13px;
            margin: 0 3px;
            border-radius: 8px;
            text-decoration: none;
            color: #2563eb;
            background: #fff;
            border: 1px solid #e2e8f0;
            font-size: 14px;
        }

        .pagination .active span {
            background: #2563eb;
            color: #fff;
        }

        /* ===== Responsive ===== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 90;
        }

        @media (max-width: 960px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main {
                margin-left: 0;
            }
            .hamburger {
                display: block;
            }
            .sidebar-overlay.show {
                display: block;
            }
            .grid-4 { grid-template-columns: repeat(2, 1fr); }
            .grid-3 { grid-template-columns: repeat(2, 1fr); }
            .grid-2 { grid-template-columns: 1fr; }
        }

        @media (max-width: 600px) {
            .grid-4, .grid-3 { grid-template-columns: 1fr; }
            .form-row { grid-template-columns: 1fr; }
            .content { padding: 18px; }
            .topbar { padding: 0 18px; }
        }
    </style>
    @stack('styles')
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="logo">P</div>
            <div>
                <div class="brand-text">Professor</div>
                <div class="brand-sub">Personal Finance</div>
            </div>
        </div>

        <nav class="nav">
            <div class="nav-label">Main</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="icon">📊</span> Overview
            </a>
            <a href="{{ route('admin.index') }}" class="nav-item {{ request()->routeIs('admin.index') ? 'active' : '' }}">
                <span class="icon">🛠️</span> Admin Panel
            </a>
<a href="{{ route('transactions.index') }}" class="nav-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                <span class="icon">💳</span> Transactions
            </a>
<a href="{{ route('income.index') }}" class="nav-item {{ request()->routeIs('income.*') ? 'active' : '' }}">
                <span class="icon">💰</span> Income
            </a>
            <a href="{{ route('expense.index') }}" class="nav-item {{ request()->routeIs('expense.*') ? 'active' : '' }}">
                <span class="icon">🛒</span> Expenses
            </a>

            <div class="nav-label">Finance</div>
            <a href="{{ route('accounts.index') }}" class="nav-item {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                <span class="icon">🏦</span> Accounts / Wallets
            </a>
            <a href="{{ route('categories.index') }}" class="nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <span class="icon">🏷️</span> Categories
            </a>
            <a href="{{ route('budgets.index') }}" class="nav-item {{ request()->routeIs('budgets.*') ? 'active' : '' }}">
                <span class="icon">🎯</span> Budgets
            </a>
            <a href="{{ route('savings-goals.index') }}" class="nav-item {{ request()->routeIs('savings-goals.*') ? 'active' : '' }}">
                <span class="icon">💰</span> Savings Goals
            </a>
            <a href="{{ route('loans.index') }}" class="nav-item {{ request()->routeIs('loans.*') ? 'active' : '' }}">
                <span class="icon">🏷️</span> Loans / Debts
            </a>
            <a href="{{ route('recurrings.index') }}" class="nav-item {{ request()->routeIs('recurrings.*') ? 'active' : '' }}">
                <span class="icon">🔁</span> Recurring
            </a>

            <div class="nav-label">Insights</div>
            <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <span class="icon">📈</span> Reports & Analytics
            </a>

            <div class="nav-label">Account</div>
            <a href="{{ route('notifications.index') }}" class="nav-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                <span class="icon">🔔</span> Notifications
            </a>
            <a href="{{ route('profile.show') }}" class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <span class="icon">👤</span> Profile
            </a>
            <a href="{{ route('settings.index') }}" class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <span class="icon">⚙️</span> Settings
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-box">
                <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div style="min-width:0">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-email">{{ Auth::user()->email }}</div>
                </div>
            </div>
        </div>
    </aside>

    <div class="main">
        <div class="topbar">
            <div style="display:flex;align-items:center;gap:12px">
                <button class="hamburger" onclick="toggleSidebar()">☰</button>
                <div class="page-title">@yield('page-title', 'Overview')</div>
            </div>
            <div class="topbar-actions">
                <a href="{{ route('notifications.index') }}" class="topbar-link" title="Notifications">
                    🔔
                    @if($unreadNotificationsCount = \App\Models\AppNotification::where('user_id', Auth::id())->where('is_read', false)->count())
                        <span class="badge-count">{{ $unreadNotificationsCount }}</span>
                    @endif
                </a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-sm">Logout</button>
                </form>
            </div>
        </div>

        <div class="content">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <ul style="margin-left:18px">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }
    </script>
    @stack('scripts')
</body>
</html>
