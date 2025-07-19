@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home /</span> PWA Status</h4>

    <div class="row">
        <!-- PWA Installation Status -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">PWA Installation</h5>
                    <span class="badge bg-primary" id="pwa-status">Not Installed</span>
                </div>
                <div class="card-body">
                    <p class="card-text">Install BinaniAir Library as a Progressive Web App for better performance and offline access.</p>
                    <button id="install-pwa-btn" class="btn btn-primary" disabled>
                        <i class="mdi mdi-download"></i> Install App
                    </button>
                </div>
            </div>
        </div>

        <!-- Connection Status -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Connection Status</h5>
                    <span class="badge" id="connection-badge">
                        <i class="mdi mdi-loading mdi-spin"></i> Checking...
                    </span>
                </div>
                <div class="card-body">
                    <p class="card-text" id="connection-text">Checking network connectivity...</p>
                    <div class="d-flex align-items-center mt-3">
                        <div class="spinner-border spinner-border-sm me-2" id="connection-spinner"></div>
                        <span id="connection-details">Initializing...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Cache Statistics -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Offline Cache Status</h5>
                    <button id="refresh-cache-stats" class="btn btn-outline-primary btn-sm">
                        <i class="mdi mdi-refresh"></i> Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 col-lg-3 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="mdi mdi-book"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0" id="cached-manuals">0</h6>
                                    <small class="text-muted">Cached Manuals</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-info">
                                        <i class="mdi mdi-folder"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0" id="cached-items">0</h6>
                                    <small class="text-muted">Cached Items</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-success">
                                        <i class="mdi mdi-file"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0" id="cached-content">0</h6>
                                    <small class="text-muted">Cached Content</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-warning">
                                        <i class="mdi mdi-database"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0" id="cache-metadata">0</h6>
                                    <small class="text-muted">Cache Metadata</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <small class="text-muted">Last sync: <span id="last-sync">Never</span></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cache Management -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Cache Management</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button id="sync-data-btn" class="btn btn-success">
                            <i class="mdi mdi-sync"></i> Sync Data
                        </button>
                        <button id="clear-cache-btn" class="btn btn-warning">
                            <i class="mdi mdi-delete-sweep"></i> Clear Cache
                        </button>
                        <button id="force-refresh-btn" class="btn btn-info">
                            <i class="mdi mdi-refresh"></i> Force Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Offline Search -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Offline Search</h5>
        </div>
        <div class="card-body">
            <div class="input-group mb-3">
                <input type="text" class="form-control" id="offline-search" placeholder="Search cached manuals...">
                <button class="btn btn-outline-secondary" type="button" id="search-btn">
                    <i class="mdi mdi-magnify"></i>
                </button>
            </div>
            <div id="search-results" class="mt-3"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let isInstalled = false;

    document.addEventListener('DOMContentLoaded', function() {
        initializePWAStatus();
        setupEventListeners();
        updatePWAConnectionStatus();
        setupAutoRefresh();
        
        // Check if storage is already ready
        if (window.libraryStorageReady) {
            loadCacheStats();
        } else {
            // Listen for storage ready event
            window.addEventListener('libraryStorageReady', function() {
                console.log('[PWA Status] Library storage is ready, loading cache stats...');
                loadCacheStats();
            });
            
            // Also try with retry as fallback
            loadCacheStatsWithRetry();
        }
    });

    // Enhanced cache loading with retry mechanism and visual feedback
    async function loadCacheStatsWithRetry(maxRetries = 5) {
        console.log('[PWA Status] Starting cache stats loading with retry...');
        
        // Show loading state
        showCacheLoadingState();
        
        for (let attempt = 1; attempt <= maxRetries; attempt++) {
            console.log(`[PWA Status] Cache stats loading attempt ${attempt}/${maxRetries}`);
            
            try {
                // Check if libraryStorage exists and is supported
                if (window.libraryStorage && window.libraryStorage.isSupported) {
                    // Wait for the database to be initialized
                    if (!window.libraryStorage.db) {
                        console.log('[PWA Status] Database not yet initialized, waiting...');
                        await window.libraryStorage.init();
                    }
                    
                    // Now try to load cache stats
                    await loadCacheStats();
                    hideCacheLoadingState();
                    console.log('[PWA Status] Cache stats loaded successfully');
                    return;
                }
            } catch (error) {
                console.warn(`[PWA Status] Attempt ${attempt} failed:`, error);
            }
            
            // Wait before retrying (exponential backoff)
            if (attempt < maxRetries) {
                const delay = Math.min(1000 * Math.pow(2, attempt - 1), 5000);
                console.log(`[PWA Status] Retrying in ${delay}ms...`);
                await new Promise(resolve => setTimeout(resolve, delay));
            }
        }
        
        console.warn('[PWA Status] Failed to load cache stats after all retries');
        showCacheLoadError();
    }

    function showCacheLoadingState() {
        const elements = ['cached-manuals', 'cached-items', 'cached-content', 'cache-metadata'];
        elements.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i>';
            }
        });
        document.getElementById('last-sync').innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Loading...';
    }

    function hideCacheLoadingState() {
        // Loading indicators will be replaced by actual data in loadCacheStats()
    }

    function showCacheLoadError() {
        const elements = ['cached-manuals', 'cached-items', 'cached-content', 'cache-metadata'];
        elements.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.innerHTML = '<i class="mdi mdi-alert-circle text-danger"></i>';
            }
        });
        document.getElementById('last-sync').innerHTML = '<span class="text-danger">Failed to load</span>';
    }

    function setupAutoRefresh() {
        // Auto-refresh cache stats every 30 seconds when page is visible
        let autoRefreshInterval;
        
        function startAutoRefresh() {
            if (autoRefreshInterval) return; // Already running
            
            autoRefreshInterval = setInterval(() => {
                if (!document.hidden && navigator.onLine) {
                    console.log('[PWA Status] Auto-refreshing cache stats...');
                    loadCacheStats();
                }
            }, 30000); // 30 seconds
        }
        
        function stopAutoRefresh() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshInterval = null;
            }
        }
        
        // Start auto-refresh
        startAutoRefresh();
        
        // Handle page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopAutoRefresh();
            } else {
                startAutoRefresh();
                // Refresh immediately when page becomes visible
                loadCacheStats();
            }
        });
        
        // Handle online/offline events
        window.addEventListener('online', () => {
            console.log('[PWA Status] Back online - refreshing cache stats');
            loadCacheStats();
            startAutoRefresh();
        });
        
        window.addEventListener('offline', () => {
            console.log('[PWA Status] Gone offline - stopping auto-refresh');
            stopAutoRefresh();
        });
        
        // Handle page focus/blur
        window.addEventListener('focus', () => {
            console.log('[PWA Status] Page focused - refreshing cache stats');
            loadCacheStats();
        });
    }

    function initializePWAStatus() {
        console.log('[PWA Status] Initializing PWA status...');
        
        // Check if app is already installed using multiple methods
        const isRunningStandalone = checkIfPWAInstalled();
        
        if (isRunningStandalone) {
            isInstalled = true;
            updatePWAStatus('Installed', 'success');
            document.getElementById('install-pwa-btn').style.display = 'none';
            console.log('[PWA Status] PWA is installed and running in standalone mode');
        } else {
            // Check if installation is available
            checkInstallationAvailability();
        }

        // Listen for install prompt
        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('[PWA Status] Install prompt available');
            e.preventDefault();
            window.deferredPrompt = e;
            if (!isInstalled) {
                updatePWAStatus('Available', 'warning');
                document.getElementById('install-pwa-btn').disabled = false;
            }
        });

        // Listen for app installed
        window.addEventListener('appinstalled', () => {
            console.log('[PWA Status] App installed event fired');
            isInstalled = true;
            updatePWAStatus('Installed', 'success');
            document.getElementById('install-pwa-btn').style.display = 'none';
            window.deferredPrompt = null;
        });
    }
    
    function checkIfPWAInstalled() {
        console.log('[PWA Status] Checking PWA installation status...');
        
        // Method 1: Check display mode (most reliable for desktop)
        if (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) {
            console.log('[PWA Status] PWA detected via display-mode: standalone');
            return true;
        }
        
        // Method 2: iOS Safari standalone mode
        if (window.navigator.standalone === true) {
            console.log('[PWA Status] PWA detected via navigator.standalone (iOS)');
            return true;
        }
        
        // Method 3: Check URL parameters (some PWAs add this)
        if (window.location.search.includes('utm_source=pwa') || window.location.search.includes('source=pwa')) {
            console.log('[PWA Status] PWA detected via URL parameters');
            return true;
        }
        
        // Method 4: Check referrer (when launched from home screen)
        if (document.referrer === '' && window.location.pathname !== '/') {
            console.log('[PWA Status] PWA potentially detected via referrer check');
            // This is less reliable, so we'll use it as a hint
        }
        
        // Method 5: Check window dimensions and user agent (for mobile)
        if (window.innerHeight === screen.height && /Mobi|Android/i.test(navigator.userAgent)) {
            console.log('[PWA Status] PWA potentially detected via fullscreen mobile check');
            // This is also less reliable
        }
        
        console.log('[PWA Status] No PWA installation detected');
        return false;
    }
    
    function checkInstallationAvailability() {
        console.log('[PWA Status] Checking installation availability...');
        
        // Check if beforeinstallprompt has already fired
        if (window.deferredPrompt) {
            updatePWAStatus('Available', 'warning');
            document.getElementById('install-pwa-btn').disabled = false;
            return;
        }
        
        // Check if service worker is registered (indication PWA is ready)
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistrations().then(registrations => {
                if (registrations.length > 0) {
                    console.log('[PWA Status] Service worker found, PWA installable');
                    updatePWAStatus('Ready', 'info');
                } else {
                    console.log('[PWA Status] No service worker found');
                    updatePWAStatus('Not Available', 'secondary');
                }
            }).catch(error => {
                console.error('[PWA Status] Error checking service worker:', error);
                updatePWAStatus('Unknown', 'secondary');
            });
        } else {
            console.log('[PWA Status] Service worker not supported');
            updatePWAStatus('Not Supported', 'secondary');
        }
        
        // Set a timeout to change status if no install prompt appears
        setTimeout(() => {
            if (!window.deferredPrompt && !isInstalled) {
                updatePWAStatus('Not Available', 'secondary');
                document.getElementById('install-pwa-btn').disabled = true;
            }
        }, 3000);
    }

    function updatePWAStatus(status, type) {
        const statusBadge = document.getElementById('pwa-status');
        statusBadge.textContent = status;
        statusBadge.className = `badge bg-${type}`;
    }

    function updatePWAConnectionStatus() {
        console.log('[PWA Status] Updating connection status, navigator.onLine:', navigator.onLine);
        
        const badge = document.getElementById('connection-badge');
        const text = document.getElementById('connection-text');
        const details = document.getElementById('connection-details');
        const spinner = document.getElementById('connection-spinner');

        if (!badge || !text || !details || !spinner) {
            console.warn('[PWA Status] Connection status elements not found');
            return;
        }

        if (navigator.onLine) {
            badge.innerHTML = '<i class="mdi mdi-wifi"></i> Online';
            badge.className = 'badge bg-success';
            text.textContent = 'You are connected to the internet. All features are available.';
            details.textContent = 'Full functionality available';
            spinner.style.display = 'none';
        } else {
            badge.innerHTML = '<i class="mdi mdi-wifi-off"></i> Offline';
            badge.className = 'badge bg-danger';
            text.textContent = 'You are offline. Only cached content is available.';
            details.textContent = 'Limited to cached data';
            spinner.style.display = 'none';
        }
    }

    async function loadCacheStats() {
        console.log('[PWA Status] Loading cache stats...');
        
        if (!window.libraryStorage) {
            console.warn('[PWA Status] libraryStorage not available, retrying in 1 second');
            setTimeout(loadCacheStats, 1000);
            return;
        }

        try {
            console.log('[PWA Status] Calling getCacheStats...');
            const stats = await window.libraryStorage.getCacheStats();
            
            console.log('[PWA Status] Got stats:', stats);
            
            if (stats) {
                document.getElementById('cached-manuals').textContent = stats.manualsCount || 0;
                document.getElementById('cached-items').textContent = stats.itemsCount || 0;
                document.getElementById('cached-content').textContent = stats.contentCount || 0;
                document.getElementById('cache-metadata').textContent = stats.cacheMetadata || 0;
                
                const lastSync = stats.lastSync;
                if (lastSync) {
                    const syncDate = new Date(lastSync);
                    document.getElementById('last-sync').textContent = syncDate.toLocaleString();
                } else {
                    document.getElementById('last-sync').textContent = 'Never';
                }
                
                console.log('[PWA Status] Cache stats updated successfully');
            } else {
                console.warn('[PWA Status] No stats returned from getCacheStats');
            }
        } catch (error) {
            console.error('[PWA Status] Failed to load cache stats:', error);
        }
    }

    function setupEventListeners() {
        // Install PWA button
        document.getElementById('install-pwa-btn').addEventListener('click', async () => {
            if (window.deferredPrompt) {
                window.deferredPrompt.prompt();
                const { outcome } = await window.deferredPrompt.userChoice;
                console.log(`User response to the install prompt: ${outcome}`);
                window.deferredPrompt = null;
            }
        });

        // Refresh cache stats
        document.getElementById('refresh-cache-stats').addEventListener('click', () => {
            const btn = document.getElementById('refresh-cache-stats');
            const originalHtml = btn.innerHTML;
            
            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Refreshing...';
            btn.disabled = true;
            
            loadCacheStats().finally(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            });
        });

        // Sync data
        document.getElementById('sync-data-btn').addEventListener('click', async () => {
            const btn = document.getElementById('sync-data-btn');
            const originalHtml = btn.innerHTML;
            
            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Syncing...';
            btn.disabled = true;

            try {
                if (window.libraryStorage) {
                    await window.libraryStorage.syncOnline();
                    showNotification('success', 'Data synced successfully!');
                    loadCacheStats();
                }
            } catch (error) {
                showNotification('error', 'Failed to sync data');
            } finally {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        });

        // Clear cache
        document.getElementById('clear-cache-btn').addEventListener('click', async () => {
            if (confirm('Are you sure you want to clear all cached data? This will remove all offline content.')) {
                const btn = document.getElementById('clear-cache-btn');
                const originalHtml = btn.innerHTML;
                
                btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Clearing...';
                btn.disabled = true;

                try {
                    if (window.libraryStorage) {
                        await window.libraryStorage.clearAllData();
                        showNotification('success', 'Cache cleared successfully!');
                        loadCacheStats();
                    }

                    // Clear service worker caches
                    if ('caches' in window) {
                        const cacheNames = await caches.keys();
                        await Promise.all(
                            cacheNames.map(cacheName => caches.delete(cacheName))
                        );
                    }
                } catch (error) {
                    showNotification('error', 'Failed to clear cache');
                } finally {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }
            }
        });

        // Force refresh
        document.getElementById('force-refresh-btn').addEventListener('click', () => {
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.getRegistrations().then(function(registrations) {
                    for(let registration of registrations) {
                        registration.unregister();
                    }
                    window.location.reload(true);
                });
            } else {
                window.location.reload(true);
            }
        });

        // Offline search
        document.getElementById('search-btn').addEventListener('click', performOfflineSearch);
        document.getElementById('offline-search').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                performOfflineSearch();
            }
        });

        // Connection status listeners
        window.addEventListener('online', updatePWAConnectionStatus);
        window.addEventListener('offline', updatePWAConnectionStatus);
    }

    async function performOfflineSearch() {
        const query = document.getElementById('offline-search').value.trim();
        const resultsContainer = document.getElementById('search-results');

        if (!query) {
            resultsContainer.innerHTML = '<p class="text-muted">Please enter a search term.</p>';
            return;
        }

        if (!window.libraryStorage) {
            resultsContainer.innerHTML = '<p class="text-danger">Offline storage is not available.</p>';
            return;
        }

        try {
            const results = await window.libraryStorage.searchManuals(query);
            
            if (results.length === 0) {
                resultsContainer.innerHTML = '<p class="text-muted">No cached manuals found matching your search.</p>';
                return;
            }

            let html = '<div class="list-group">';
            results.forEach(manual => {
                html += `
                    <a href="/manual/sub-manuals/${manual.id}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">${manual.name}</h6>
                            <small class="text-muted">Cached</small>
                        </div>
                        <small>Cached on: ${new Date(manual.cachedAt).toLocaleString()}</small>
                    </a>
                `;
            });
            html += '</div>';

            resultsContainer.innerHTML = html;
        } catch (error) {
            console.error('Search failed:', error);
            resultsContainer.innerHTML = '<p class="text-danger">Search failed. Please try again.</p>';
        }
    }

    function showNotification(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'mdi-check-circle' : 'mdi-alert-circle';
        
        const notification = $(`
            <div class="alert ${alertClass} alert-dismissible position-fixed" style="top: 20px; right: 20px; z-index: 1060; max-width: 350px;">
                <i class="mdi ${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(notification);
        
        setTimeout(() => {
            notification.alert('close');
        }, 5000);
    }
</script>
@endpush
@endsection
