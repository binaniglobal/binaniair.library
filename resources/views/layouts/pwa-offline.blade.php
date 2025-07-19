<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-style layout-navbar-fixed layout-compact" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>
    <title>@yield('title', 'BinaniAir Library - Offline')</title>
    <meta name="description" content="BinaniAir Library - Offline Mode"/>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#6777ef">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="BinaniAir Library">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}"/>
    
    <!-- Basic Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f9;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 8px;
        }
        
        .header h1 {
            color: #6777ef;
            margin-bottom: 10px;
        }
        
        .offline-notice {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .offline-notice i {
            margin-right: 10px;
            font-size: 20px;
        }
        
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .card h2 {
            color: #6777ef;
            margin-bottom: 15px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: #6777ef;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a67d8;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .list-group {
            list-style: none;
        }
        
        .list-group-item {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .list-group-item:last-child {
            border-bottom: none;
        }
        
        .list-group-item a {
            color: #6777ef;
            text-decoration: none;
            font-weight: 500;
        }
        
        .list-group-item a:hover {
            text-decoration: underline;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            background: #d4edda;
            color: #155724;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            color: #6c757d;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .header {
                margin-bottom: 20px;
                padding: 15px;
            }
            
            .card {
                padding: 15px;
            }
        }
    </style>
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <i class="fas fa-book"></i>
                BinaniAir Library
            </h1>
            <p>Your offline library management system</p>
        </div>
        
        <div class="offline-notice">
            <i class="fas fa-wifi-slash"></i>
            <div>
                <strong>You're currently offline.</strong> 
                Only cached content is available. Connect to the internet to access all features.
            </div>
        </div>
        
        @yield('content')
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} BinaniAir Library. All rights reserved.</p>
            <p>Progressive Web App - Offline Mode</p>
        </div>
    </div>
    
    <!-- PWA Storage Script -->
    <script>
        // Basic offline functionality
        function checkOnlineStatus() {
            if (navigator.onLine) {
                window.location.reload();
            }
        }
        
        // Check for online status every 5 seconds
        setInterval(checkOnlineStatus, 5000);
        
        // Listen for online event
        window.addEventListener('online', checkOnlineStatus);
        
        // Service worker registration
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(function(registration) {
                    console.log('Service Worker registered successfully:', registration.scope);
                })
                .catch(function(error) {
                    console.log('Service Worker registration failed:', error);
                });
        }
    </script>
    
    @stack('scripts')
</body>
</html>
