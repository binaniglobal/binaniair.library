{{--
    REFACTORED:
    - Standardized asset paths using the asset() helper.
    - Simplified and secured the logic for "active" menu items.
    - Refactored user info display to be safer and more efficient.
    - Removed duplicate code for the brand logo and user avatar.
    - Kept PWA script block intact while cleaning surrounding code.
--}}
    <!doctype html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="light-style layout-navbar-fixed layout-compact"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="/storage/assets/"
    data-template="front-pages">
<head>
    <meta charset="utf-8"/>
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>

    <title>BinaniAir Library</title>

    <meta name="description" content="BinaniAir Library"/>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#6777ef">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="BinaniAir Library">

    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ getGlobalImage('Favicon') }}"/>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer"/>

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/fonts/materialdesignicons.css') }}"/>
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/libs/node-waves/node-waves.css') }}"/>
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css"/>
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/css/rtl/theme-default.css') }}" class="template-customizer-theme-css"/>
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/css/pages/front-page.css') }}"/>

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}"/>
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/libs/typeahead-js/typeahead.css') }}"/>
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/libs/swiper/swiper.css') }}"/>
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/libs/flatpickr/flatpickr.css') }}"/>
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/libs/select2/select2.css') }}"/>

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/css/pages/front-page-landing.css') }}"/>
    @stack('styles')

    <!-- Helpers -->
    <script src="{{ asset('storage/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('storage/assets/vendor/js/template-customizer.js') }}"></script>
    <script src="{{ asset('storage/assets/js/config.js') }}"></script>
</head>

<body>

<script src="{{ asset('storage/assets/vendor/js/dropdown-hover.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/js/mega-dropdown.js') }}"></script>

@php
    // Get the authenticated user once to avoid multiple database calls.
    $user = auth()->user();
    // Safely get the role name using optional chaining `?->`. This prevents errors if a user has no roles.
    $roleName = $user?->roles?->first()?->name;
@endphp

    <!-- Layout wrapper -->
<div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
    <div class="layout-container">
        <!-- Navbar -->
        <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
            <div class="container-xxl">
                <div class="navbar-brand app-brand d-none d-xl-flex py-0 me-4">
                    {{-- REFACTORED: Simplified the brand link logic to avoid repetition. --}}
                    <a href="{{ $user?->can('view-home') ? route('home') : route('manual.index') }}" class="app-brand-link gap-2">
                        <span class="app-brand-logo demo">
                            <img width="50" height="50" src="{{ getGlobalImage('Normal') }}" alt="Brand Logo">
                        </span>
                        <span class="app-brand-text demo menu-text fw-bold">Library</span>
                    </a>
                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                        <i class="mdi mdi-close align-middle"></i>
                    </a>
                </div>

                <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                        <i class="mdi mdi-menu mdi-24px"></i>
                    </a>
                </div>
                <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                    <ul class="navbar-nav flex-row align-items-center ms-auto">

                        <!-- Style Switcher -->
                        <li class="nav-item dropdown-style-switcher dropdown me-1 me-xl-0">
                            <a class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                <i class="mdi mdi-24px"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
                                <li><a class="dropdown-item" href="javascript:void(0);" data-theme="light"><span class="align-middle"><i class="mdi mdi-weather-sunny me-2"></i>Light</span></a></li>
                                <li><a class="dropdown-item" href="javascript:void(0);" data-theme="dark"><span class="align-middle"><i class="mdi mdi-weather-night me-2"></i>Dark</span></a></li>
                                <li><a class="dropdown-item" href="javascript:void(0);" data-theme="system"><span class="align-middle"><i class="mdi mdi-monitor me-2"></i>System</span></a></li>
                            </ul>
                        </li>
                        <!-- / Style Switcher-->

                        <!-- User -->
                        <li class="nav-item navbar-dropdown dropdown-user dropdown">
                            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                <div class="avatar avatar-online">
                                    <img src="{{ asset('storage/assets/img/avatars/1.png') }}" alt="User Avatar" class="w-px-40 h-auto rounded-circle"/>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar avatar-online">
                                                    <img src="{{ asset('storage/assets/img/avatars/1.png') }}" alt="User Avatar" class="w-px-40 h-auto rounded-circle"/>
                                                </div>
                                            </div>
                                            {{-- REFACTORED: Cleaned up and secured the user info display. --}}
                                            <div class="flex-grow-1">
                                                <span class="fw-medium d-block">
                                                    {{ $user->name ?? 'Guest User' }}
                                                    @if($roleName)
                                                        - ({{ ucfirst($roleName) }})
                                                    @endif
                                                </span>
                                                <small class="text-muted">{{ $user->email ?? '' }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li><div class="dropdown-divider"></div></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile') }}">
                                        <i class="mdi mdi-account-outline me-2"></i>
                                        <span class="align-middle">My Profile</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="mdi mdi-logout me-2"></i>
                                        <span class="align-middle">Log Out</span>
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                        <!--/ User -->
                    </ul>
                </div>
            </div>
        </nav>
        <!-- / Navbar -->

        <!-- Layout container -->
        <div class="layout-page">
            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Menu -->
                <aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
                    <div class="container-xxl d-flex h-100">
                        <ul class="menu-inner">
                            {{-- REFACTORED: Replaced `{{ __('active') }}` with the standard, safer way to set an active class. --}}
                            @can('view-home')
                                <li class="menu-item {{ request()->is('home') ? 'active' : '' }}">
                                    <a href="{{ route('home') }}" class="menu-link">
                                        <i class="menu-icon tf-icons mdi mdi-home-outline"></i>
                                        <div data-i18n="Home">Home</div>
                                    </a>
                                </li>
                            @endcan

                            @can('view-manual')
                                <li class="menu-item {{ request()->is('manuals') || request()->is('manual/add') || request()->is('manual/sub-manuals/*') || request()->is('manual/sub-manuals/content/*') ? 'active' : '' }}">
                                    <a href="{{ route('manual.index') }}" class="menu-link">
                                        <i class="menu-icon tf-icons mdi mdi-book-account"></i>
                                        <div data-i18n="Manuals">Manuals</div>
                                    </a>
                                </li>
                            @endcan

                            @can('view-user')
                                <li class="menu-item {{ request()->is('users*') ? 'active' : '' }}">
                                    <a href="{{ route('users.index') }}" class="menu-link">
                                        <i class="menu-icon tf-icons mdi mdi-account-group"></i>
                                        <div data-i18n="Users">Users</div>
                                    </a>
                                </li>
                            @endcan

                            <li class="menu-item {{ request()->is('pwa-status*') ? 'active' : '' }}">
                                <a href="{{ route('pwa.status') }}" class="menu-link">
                                    <i class="menu-icon tf-icons mdi mdi-account-group"></i>
                                    <div data-i18n="App Status">App Status</div>
                                </a>
                            </li>

                            @if($user?->hasRole('super-admin'))
                                <li class="menu-item {{ request()->is('roles*') || request()->is('permissions*') ? 'active open' : '' }}">
                                    <a class="menu-link menu-toggle">
                                        <i class="menu-icon tf-icons mdi mdi-cog-outline"></i>
                                        <div data-i18n="Settings">Settings</div>
                                    </a>
                                    <ul class="menu-sub">
                                        <li class="menu-item {{ request()->is('roles*') ? 'active' : '' }}">
                                            <a href="{{ route('roles') }}" class="menu-link">
                                                <i class="menu-icon tf-icons mdi mdi-shield-account"></i>
                                                <div data-i18n="Roles">Roles</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('permissions*') ? 'active' : '' }}">
                                            <a href="{{ route('permissions') }}" class="menu-link">
                                                <i class="menu-icon tf-icons mdi mdi-shield-key"></i>
                                                <div data-i18n="Permissions">Permissions</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endif
                        </ul>
                    </div>
                </aside>
                <!-- / Menu -->

                @yield('content')

                <!-- Footer -->
                <footer class="content-footer footer bg-footer-theme">
                    <div class="container-xxl">
                        <div class="footer-container d-flex align-items-center justify-content-between py-3 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                © {{ date('Y') }} All rights reserved
                            </div>
                        </div>
                    </div>
                </footer>
                <!-- / Footer -->

                <div class="content-backdrop fade"></div>
            </div>
            <!--/ Content wrapper -->
        </div>
        <!--/ Layout container -->
    </div>
</div>

<!-- Overlay -->
<div class="layout-overlay layout-menu-toggle"></div>

<!-- Drag Target Area To SlideIn Menu On Small Screens -->
<div class="drag-target"></div>

<script src="{{ asset('storage/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/libs/node-waves/node-waves.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/libs/hammer/hammer.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/libs/i18n/i18n.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/js/menu.js') }}"></script>

<!-- Vendors JS -->
<script src="{{ asset('storage/assets/vendor/libs/cleavejs/cleave.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/3.0.1/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.colVis.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.html5.min.js"></script>

<!-- Main JS -->
<script src="{{ asset('storage/assets/js/main.js') }}"></script>

<!-- CryptoJS Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js" integrity="sha512-E8QSvWZ0eCLGk4km3hxSsNmGWbLtSCSUcewDQPQWZF6pEU8GlT8a5fF32wOl1i8ftdMhssTrF/OhyGWwonTcXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- PWA Scripts -->
<script src="{{ asset('js/pwa-storage.js') }}"></script>
<script src="{{ asset('js/secure-viewer.js') }}"></script>
<script src="{{ asset('js/security-fixes.js') }}"></script>
@stack('scripts')
<script>
    // Check if service workers are supported
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js')
                .then(function(registration) {
                    console.log('[PWA] Service Worker registered successfully:', registration.scope);

                    // Check for updates
                    registration.addEventListener('updatefound', () => {
                        const newWorker = registration.installing;
                        newWorker.addEventListener('statechange', () => {
                            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                // New service worker is available
                                showUpdateAvailableNotification();
                            }
                        });
                    });
                })
                .catch(function(error) {
                    console.log('[PWA] Service Worker registration failed:', error);
                });
        });
    }

    // PWA Install prompt
    let deferredPrompt;

    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent the mini-infobar from appearing on mobile
        e.preventDefault();
        // Stash the event so it can be triggered later.
        deferredPrompt = e;
        // Show the install button
        showInstallButton();
    });

    function showInstallButton() {
        // Create install button if it doesn't exist
        if (!document.getElementById('pwa-install-btn')) {
            const installBtn = document.createElement('button');
            installBtn.id = 'pwa-install-btn';
            installBtn.innerHTML = '<i class="mdi mdi-download"></i> Install App';
            installBtn.className = 'btn btn-primary btn-sm position-fixed';
            installBtn.style.cssText = 'bottom: 20px; right: 20px; z-index: 1050; border-radius: 25px; padding: 8px 16px; font-size: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';

            installBtn.addEventListener('click', async () => {
                if (deferredPrompt) {
                    // Show the install prompt
                    deferredPrompt.prompt();
                    // Wait for the user to respond to the prompt
                    const { outcome } = await deferredPrompt.userChoice;
                    console.log(`[PWA] User response to the install prompt: ${outcome}`);
                    // Clear the deferred prompt
                    deferredPrompt = null;
                    // Hide the install button
                    installBtn.remove();
                }
            });

            document.body.appendChild(installBtn);
        }
    }

    function showUpdateAvailableNotification() {
        // Create update notification
        const updateNotification = document.createElement('div');
        updateNotification.className = 'alert alert-info position-fixed';
        updateNotification.style.cssText = 'top: 20px; right: 20px; z-index: 1060; max-width: 300px;';
        updateNotification.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="mdi mdi-information me-2"></i>
                <div class="flex-grow-1">
                    <strong>App Update Available</strong><br>
                    <small>New features and improvements are ready.</small>
                </div>
                <button class="btn btn-sm btn-outline-primary ms-2" onclick="updateApp()">Update</button>
            </div>
        `;

        document.body.appendChild(updateNotification);

        // Auto-hide after 10 seconds
        setTimeout(() => {
            updateNotification.remove();
        }, 10000);
    }

    function updateApp() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistration().then(function(registration) {
                if (registration && registration.waiting) {
                    registration.waiting.postMessage({ type: 'SKIP_WAITING' });
                    window.location.reload();
                }
            });
        }
    }

    // Cache manual data for offline access
    function cacheManualData() {
        if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
            // Get all manuals data from the page
            const manuals = [];

            // Extract manual data from the page (if available)
            const manualLinks = document.querySelectorAll('a[href*="/manual/sub-manuals/"]');
            manualLinks.forEach(link => {
                const href = link.getAttribute('href');
                const match = href.match(/\/manual\/sub-manuals\/(\d+)/);
                if (match) {
                    manuals.push({ id: match[1], name: link.textContent.trim() });
                }
            });

            if (manuals.length > 0) {
                navigator.serviceWorker.controller.postMessage({
                    type: 'CACHE_MANUAL',
                    manuals: manuals
                });
            }
        }
    }

    // Cache manual data when the page loads - but only after auth token is set
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure auth token is set before caching
        async function initializePWAWithAuth() {
            console.log('[PWA] Initializing PWA with authentication...');
            
            // Wait for service worker to be ready
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            // Update auth token first
            const tokenUpdated = await updateServiceWorkerAuthToken();
            if (tokenUpdated) {
                console.log('[PWA] Auth token updated, starting manual caching...');
                // Wait a bit more to ensure service worker has processed the token
                setTimeout(cacheManualData, 500);
            } else {
                console.warn('[PWA] Failed to update auth token, caching may fail');
                // Try caching anyway in case session auth works
                setTimeout(cacheManualData, 1000);
            }
        }
        
        setTimeout(initializePWAWithAuth, 2000); // Delay to ensure page is fully loaded
    });

    // Add offline/online status indicators
    function updateConnectionStatus() {
        const isOnline = navigator.onLine;
        let statusIndicator = document.getElementById('connection-status');

        if (!statusIndicator) {
            statusIndicator = document.createElement('div');
            statusIndicator.id = 'connection-status';
            statusIndicator.className = 'position-fixed';
            statusIndicator.style.cssText = 'top: 10px; left: 50%; transform: translateX(-50%); z-index: 1060; padding: 4px 12px; border-radius: 15px; font-size: 12px; transition: all 0.3s ease;';
            document.body.appendChild(statusIndicator);
        }

        // In development, check actual network connectivity
        if (window.location.hostname === '127.0.0.1' || window.location.hostname === 'localhost') {
            checkActualConnectivity().then(actuallyOnline => {
                displayConnectionStatus(statusIndicator, actuallyOnline);
            });
        } else {
            displayConnectionStatus(statusIndicator, isOnline);
        }
    }
    
    // Check actual connectivity by making a small request
    async function checkActualConnectivity() {
        try {
            const response = await fetch('/home', {
                method: 'HEAD',
                cache: 'no-cache',
                signal: AbortSignal.timeout(5000)
            });
            return response.ok;
        } catch (error) {
            console.log('Connectivity check failed:', error);
            return false;
        }
    }
    
    function displayConnectionStatus(statusIndicator, isOnline) {
        if (isOnline) {
            statusIndicator.style.backgroundColor = '#d4edda';
            statusIndicator.style.color = '#155724';
            statusIndicator.style.border = '1px solid #c3e6cb';
            statusIndicator.innerHTML = '🟢 Online';
            statusIndicator.style.opacity = '0.8';

            // Hide after 3 seconds when coming back online
            setTimeout(() => {
                statusIndicator.style.opacity = '0';
            }, 3000);
        } else {
            statusIndicator.style.backgroundColor = '#f8d7da';
            statusIndicator.style.color = '#721c24';
            statusIndicator.style.border = '1px solid #f5c6cb';
            statusIndicator.innerHTML = '🔴 Offline';
            statusIndicator.style.opacity = '1';
        }
    }

    // Listen for online/offline events
    window.addEventListener('online', updateConnectionStatus);
    window.addEventListener('offline', updateConnectionStatus);

    // Check initial connection status
    updateConnectionStatus();
    
    // PWA Authentication Token Management
    async function updateServiceWorkerAuthToken() {
        if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
            try {
                // Fetch authentication token from server
                const response = await fetch('/pwa/auth-token', {
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (response.ok) {
                    const tokenData = await response.json();
                    
                    // Send token to service worker
                    navigator.serviceWorker.controller.postMessage({
                        type: 'SET_AUTH_TOKEN',
                        token: tokenData.token,
                        expires_at: tokenData.expires_at
                    });
                    
                    console.log('[PWA] Authentication token sent to service worker');
                    return true;
                } else {
                    console.warn('[PWA] Failed to get authentication token:', response.status);
                    return false;
                }
            } catch (error) {
                console.error('[PWA] Error updating service worker auth token:', error);
                return false;
            }
        }
        return false;
    }
    
    // Note: Token update is now handled in the PWA initialization above
    
    // Update token when service worker becomes active
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.addEventListener('controllerchange', function() {
            console.log('[PWA] Service worker controller changed');
            setTimeout(() => {
                updateServiceWorkerAuthToken();
            }, 500);
        });
    }
    
    // Refresh token periodically (every 30 minutes)
    setInterval(() => {
        updateServiceWorkerAuthToken();
    }, 30 * 60 * 1000);
</script>

</body>
</html>
