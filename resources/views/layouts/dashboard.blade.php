<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - Najaah</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 antialiased">
    
    <!-- Sidebar -->
    <aside id="sidebar" class="fixed left-0 top-0 z-40 h-screen w-64 transition-all duration-300 ease-in-out lg:translate-x-0 -translate-x-full bg-gradient-to-b from-gray-900 via-gray-900 to-gray-950 shadow-2xl">
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between h-20 px-6 border-b border-gray-800/50">
            <div class="flex items-center gap-3 sidebar-content">
                <div class="relative">
                    <div class="flex items-center justify-center w-11 h-11 bg-gradient-to-br from-red-500 to-red-700 rounded-xl shadow-lg shadow-red-500/30">
                        <span class="text-white font-black text-xl">N</span>
                    </div>
                    <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 border-2 border-gray-900 rounded-full"></div>
                </div>
                <div class="sidebar-text">
                    <span class="text-xl font-black text-white tracking-tight">Najaah</span>
                    <p class="text-xs text-gray-400 font-medium">Admin Portal</p>
                </div>
            </div>
            <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg hover:bg-gray-800 transition-colors">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <!-- Navigation -->
        <nav class="flex flex-col h-[calc(100vh-5rem)] overflow-hidden">
            <div class="flex-1 overflow-y-auto px-3 py-6 space-y-8 custom-scrollbar">
                <!-- Main Section -->
                <div class="space-y-2">
                    <div class="px-3 mb-3 sidebar-text">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest">Main Menu</h3>
                    </div>
                    
                    <a href="#" class="nav-item active group">
                        <div class="nav-icon-wrapper">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        </div>
                        <span class="sidebar-text font-medium">Dashboard</span>
                        <div class="nav-indicator"></div>
                    </a>
                    
                    <a href="#" class="nav-item group">
                        <div class="nav-icon-wrapper">
<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0-3h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
</svg>
                        </div>
                        <span class="sidebar-text font-medium">QRs</span>
                        <span class="nav-badge sidebar-text">12</span>
                    </a>
                    
                    <a href="#" class="nav-item group">
                        <div class="nav-icon-wrapper">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <span class="sidebar-text font-medium">Users</span>
                    </a>
                </div>
                
                <!-- Management Section -->
                <div class="space-y-2">
                    <div class="px-3 mb-3 sidebar-text">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest">Management</h3>
                    </div>
                    
                    <a href="#" class="nav-item group">
                        <div class="nav-icon-wrapper">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <span class="sidebar-text font-medium">Orders</span>
                        <span class="nav-badge nav-badge-warning sidebar-text">5</span>
                    </a>
                    
                    <a href="#" class="nav-item group">
                        <div class="nav-icon-wrapper">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <span class="sidebar-text font-medium">Products</span>
                    </a>
                    
                    <a href="#" class="nav-item group">
                        <div class="nav-icon-wrapper">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="sidebar-text font-medium">Subscriptions</span>
                    </a>
                </div>
                
                <!-- Analytics Section -->
                <div class="space-y-2">
                    <div class="px-3 mb-3 sidebar-text">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest">Analytics</h3>
                    </div>
                    
                    <a href="#" class="nav-item group">
                        <div class="nav-icon-wrapper">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <span class="sidebar-text font-medium">Statistics</span>
                    </a>
                    
                    <a href="#" class="nav-item group">
                        <div class="nav-icon-wrapper">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="sidebar-text font-medium">Reports</span>
                    </a>
                </div>
                
                <!-- System Section -->
                <div class="space-y-2">
                    <div class="px-3 mb-3 sidebar-text">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest">System</h3>
                    </div>
                    
                    <a href="#" class="nav-item group">
                        <div class="nav-icon-wrapper">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <span class="sidebar-text font-medium">Settings</span>
                    </a>
                    
                    <a href="#" class="nav-item group">
                        <div class="nav-icon-wrapper">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <span class="sidebar-text font-medium">Support</span>
                    </a>
                </div>
            </div>
            
            <!-- User Profile -->
            <div class="px-3 pb-4 border-t border-gray-800/50 pt-4 mt-auto">
                <div class="relative">
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-800/50 hover:bg-gray-800 cursor-pointer transition-all duration-200 group sidebar-content">
                        <div class="relative flex-shrink-0">
                            <div class="flex items-center justify-center w-11 h-11 bg-gradient-to-br from-red-500 to-red-700 rounded-xl text-white font-bold shadow-lg shadow-red-500/20">
                                {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                            </div>
                            <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-green-500 border-2 border-gray-900 rounded-full"></div>
                        </div>
                        <div class="flex-1 min-w-0 sidebar-text">
                            <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name ?? 'Admin User' }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email ?? 'admin@Najaah.com' }}</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-300 transition-colors flex-shrink-0 sidebar-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </div>
        </nav>
    </aside>
    
    <!-- Sidebar Collapse Button (Desktop) -->
    <button onclick="collapseSidebar()" id="collapseBtn" class="hidden lg:flex fixed left-64 top-24 z-50 items-center justify-center w-7 h-7 bg-white border border-gray-200 rounded-full shadow-lg hover:shadow-xl hover:scale-110 transition-all duration-300 group">
        <svg id="collapseIcon" class="w-4 h-4 text-gray-600 group-hover:text-gray-900 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>
    
    <!-- Main Content -->
    <div id="mainContent" class="lg:ml-64 transition-all duration-300">
        <!-- Top Header -->
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-xl border-b border-gray-200/80 shadow-sm">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-4">
                    <!-- Mobile Menu Button -->
                    <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-xl hover:bg-gray-100 transition-colors">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    
                    <!-- Page Title -->
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black text-gray-900">{{ $title ?? 'Dashboard' }}</h1>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">Welcome back, manage your workspace</p>
                    </div>
                </div>
                
                <!-- Header Actions -->
                <div class="flex items-center gap-2">
                    <!-- Search Button -->
                    <button class="p-2.5 rounded-xl hover:bg-gray-100 transition-colors group">
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                    
                    <!-- Notifications -->
                    <button class="relative p-2.5 rounded-xl hover:bg-gray-100 transition-colors group">
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute top-1.5 right-1.5 flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                        </span>
                    </button>
                    
                    <!-- User Menu -->
                    <button class="hidden sm:flex items-center gap-2 p-1.5 rounded-xl hover:bg-gray-100 transition-colors">
                        <div class="w-9 h-9 bg-gradient-to-br from-red-500 to-red-700 rounded-xl flex items-center justify-center text-white text-sm font-bold shadow-lg shadow-red-500/20">
                            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                        </div>
                    </button>
                </div>
            </div>
        </header>
        
        <!-- Page Content -->
        <main class="p-4 sm:p-6 lg:p-8 min-h-screen">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
                
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-800 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('errors'))
                <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-800 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach (session('errors')->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>    
            @endif
            {{ $slot }}
        </main>
    </div>
    
    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-30 lg:hidden hidden transition-opacity" onclick="toggleSidebar()"></div>
    
    <script>
        // Sidebar state
        let sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        
        // Apply initial state
        if (sidebarCollapsed) {
            applySidebarCollapsed();
        }
        
        // Toggle sidebar for mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
        
        // Collapse/Expand sidebar for desktop
        function collapseSidebar() {
            sidebarCollapsed = !sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
            
            if (sidebarCollapsed) {
                applySidebarCollapsed();
            } else {
                applySidebarExpanded();
            }
        }
        
        function applySidebarCollapsed() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const collapseBtn = document.getElementById('collapseBtn');
            const collapseIcon = document.getElementById('collapseIcon');
            
            sidebar.classList.add('w-20');
            sidebar.classList.remove('w-64');
            mainContent.classList.add('lg:ml-20');
            mainContent.classList.remove('lg:ml-64');
            collapseBtn.classList.add('left-20');
            collapseBtn.classList.remove('left-64');
            collapseIcon.classList.add('rotate-180');
            
            // Hide text and content elements
            document.querySelectorAll('.sidebar-text').forEach(el => {
                el.classList.add('opacity-0', 'invisible');
            });
            
            document.querySelectorAll('.sidebar-content').forEach(el => {
                el.classList.add('justify-center');
            });
        }
        
        function applySidebarExpanded() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const collapseBtn = document.getElementById('collapseBtn');
            const collapseIcon = document.getElementById('collapseIcon');
            
            sidebar.classList.remove('w-20');
            sidebar.classList.add('w-64');
            mainContent.classList.remove('lg:ml-20');
            mainContent.classList.add('lg:ml-64');
            collapseBtn.classList.remove('left-20');
            collapseBtn.classList.add('left-64');
            collapseIcon.classList.remove('rotate-180');
            
            // Show text and content elements
            document.querySelectorAll('.sidebar-text').forEach(el => {
                el.classList.remove('opacity-0', 'invisible');
            });
            
            document.querySelectorAll('.sidebar-content').forEach(el => {
                el.classList.remove('justify-center');
            });
        }
    </script>
    
    <style>
        /* Navigation Item Styles */
        .nav-item {
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            color: rgb(156 163 175);
            transition: all 0.2s;
            cursor: pointer;
        }
        
        .nav-item:hover {
            background-color: rgba(31, 41, 55, 0.5);
            color: white;
        }
        
        .nav-item.active {
            background: linear-gradient(to right, rgba(239, 68, 68, 0.1), transparent);
            color: white;
            box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.1);
        }
        
        .nav-item.active .nav-icon-wrapper {
            background: linear-gradient(to bottom right, rgb(239, 68, 68), rgb(185, 28, 28));
            color: white;
            box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3);
        }
        
        .nav-item:not(.active) .nav-icon-wrapper {
            background-color: rgba(31, 41, 55, 0.5);
            color: rgb(156 163 175);
            transition: all 0.2s;
        }
        
        .nav-item:not(.active):hover .nav-icon-wrapper {
            background-color: rgba(55, 65, 81, 0.5);
            color: white;
        }
        
        .nav-icon-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.5rem;
            flex-shrink: 0;
            transition: all 0.2s;
        }
        
        .nav-indicator {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 0.25rem;
            height: 2rem;
            background: linear-gradient(to bottom, rgb(239, 68, 68), rgb(185, 28, 28));
            border-radius: 0 9999px 9999px 0;
            box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.5);
        }
        
        .nav-badge {
            margin-left: auto;
            padding: 0.125rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: rgb(220, 38, 38);
            background-color: rgba(239, 68, 68, 0.1);
            border-radius: 0.5rem;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        .nav-badge-warning {
            color: rgb(217, 119, 6);
            background-color: rgba(245, 158, 11, 0.1);
            border-color: rgba(245, 158, 11, 0.2);
        }
        
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        /* Smooth transitions for sidebar text */
        .sidebar-text {
            transition: all 0.3s;
        }
        
        /* Backdrop blur support */
       
    </style>
    
    @stack('scripts')
</body>
</html>