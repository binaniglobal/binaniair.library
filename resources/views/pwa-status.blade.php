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
                    <span class="badge bg-primary" id="pwa-status">Checking...</span>
                </div>
                <div class="card-body">
                    <p class="card-text">Install BinaniAir Library as a Progressive Web App for better performance and offline access.</p>
                    <button id="install-pwa-btn" class="btn btn-primary" style="display: none;">
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
                        <div class="col-sm-6 col-lg-4 mb-3">
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
                        <div class="col-sm-6 col-lg-4 mb-3">
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
                        <div class="col-sm-6 col-lg-4 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-warning">
                                        <i class="mdi mdi-database"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0" id="total-cached-files">0</h6>
                                    <small class="text-muted">Total Cached Files</small>
                                </div>
                            </div>
                        </div>
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
                        <button id="clear-old-caches-btn" class="btn btn-info">
                            <i class="mdi mdi-broom"></i> Clear Old Static Caches
                        </button>
                        <button id="clear-cache-btn" class="btn btn-warning">
                            <i class="mdi mdi-delete-sweep"></i> Clear All Cached Files
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializePWA();
        updatePWAConnectionStatus();
        window.addEventListener('online', updatePWAConnectionStatus);
        window.addEventListener('offline', updatePWAConnectionStatus);

        loadCacheStats();
        document.getElementById('refresh-cache-stats').addEventListener('click', loadCacheStats);
        document.getElementById('clear-cache-btn').addEventListener('click', clearCache);
        document.getElementById('clear-old-caches-btn').addEventListener('click', clearOldCaches);
    });

    function initializePWA() {
        const installButton = document.getElementById('install-pwa-btn');
        const pwaStatus = document.getElementById('pwa-status');

        // The 'beforeinstallprompt' event is handled by the global script in app.blade.php
        // to avoid conflicts. This function will now only manage the UI status display.

        // Listen for the appinstalled event to update status after installation.
        window.addEventListener('appinstalled', () => {
            installButton.style.display = 'none';
            pwaStatus.textContent = 'Installed';
            pwaStatus.className = 'badge bg-success';
        });

        // Check the initial installation state.
        if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone) {
            pwaStatus.textContent = 'Installed';
            pwaStatus.className = 'badge bg-success';
            installButton.style.display = 'none';
        } else {
            // If not installed, the global script will show a floating install button when available.
            // This page will simply reflect the "Not Installed" status.
            pwaStatus.textContent = 'Not Installed';
            pwaStatus.className = 'badge bg-secondary';
            installButton.style.display = 'none'; // Hide the in-page button to prevent redundancy.
        }
    }

    function updatePWAConnectionStatus() {
        const badge = document.getElementById('connection-badge');
        const text = document.getElementById('connection-text');

        if (navigator.onLine) {
            badge.innerHTML = '<i class="mdi mdi-wifi"></i> Online';
            badge.className = 'badge bg-success';
            text.textContent = 'You are connected to the internet.';
        } else {
            badge.innerHTML = '<i class="mdi mdi-wifi-off"></i> Offline';
            badge.className = 'badge bg-danger';
            text.textContent = 'You are offline. Only cached content is available.';
        }
    }

    async function loadCacheStats() {
        const btn = document.getElementById('refresh-cache-stats');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Refreshing...';
        btn.disabled = true;

        if (!('caches' in window)) {
            document.getElementById('cached-items').textContent = 'N/A';
            document.getElementById('cached-content').textContent = 'N/A';
            document.getElementById('total-cached-files').textContent = 'N/A';
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            return;
        }

        try {
            const cache = await caches.open('library-manuals-v1.0.6');
            const requests = await cache.keys();

            let itemsCount = 0;
            let contentCount = 0;

            requests.forEach(request => {
                if (request.url.includes('/manual/sub-manuals/file/')) {
                    itemsCount++;
                } else if (request.url.includes('/manual/sub-manuals/content/')) {
                    contentCount++;
                }
            });

            document.getElementById('cached-items').textContent = itemsCount;
            document.getElementById('cached-content').textContent = contentCount;
            document.getElementById('total-cached-files').textContent = requests.length;
        } catch (error) {
            console.error('Error loading cache stats:', error);
            document.getElementById('cached-items').textContent = 'Error';
            document.getElementById('cached-content').textContent = 'Error';
            document.getElementById('total-cached-files').textContent = 'Error';
        } finally {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }
    }

    async function clearOldCaches() {
        if (!confirm('Are you sure you want to clear old static caches? This is useful after an update.')) return;

        const btn = document.getElementById('clear-old-caches-btn');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Clearing...';
        btn.disabled = true;

        try {
            const currentCaches = ['library-static-v1.0.6', 'library-dynamic-v1.0.6', 'library-manuals-v1.0.6'];
            const cacheNames = await caches.keys();
            const oldCaches = cacheNames.filter(name => !currentCaches.includes(name));

            if (oldCaches.length === 0) {
                showNotification('info', 'No old caches to clear.');
                return;
            }

            await Promise.all(oldCaches.map(cache => caches.delete(cache)));
            showNotification('success', 'Old static caches cleared successfully!');
        } catch (error) {
            console.error('Error clearing old caches:', error);
            showNotification('error', 'Failed to clear old caches.');
        } finally {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }
    }

    async function clearCache() {
        if (confirm('Are you sure you want to clear all cached PDF files?')) {
            const btn = document.getElementById('clear-cache-btn');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Clearing...';
            btn.disabled = true;

            try {
                await caches.delete('library-manuals-v1.0.6');
                showNotification('success', 'Cached PDF files cleared successfully!');
                loadCacheStats(); // Refresh stats after clearing
            } catch (error) {
                console.error('Error clearing cache:', error);
                showNotification('error', 'Failed to clear cache.');
            } finally {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
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
        setTimeout(() => notification.alert('close'), 5000);
    }

</script>
@endpush
@endsection
