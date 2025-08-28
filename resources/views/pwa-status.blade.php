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
    });

    function initializePWA() {
        let deferredPrompt;
        const installButton = document.getElementById('install-pwa-btn');
        const pwaStatus = document.getElementById('pwa-status');

        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent the mini-infobar from appearing on mobile
            e.preventDefault();
            // Stash the event so it can be triggered later.
            deferredPrompt = e;
            // Update UI to notify the user they can install the PWA
            installButton.style.display = 'block';
            pwaStatus.textContent = 'Available';
            pwaStatus.className = 'badge bg-info';
        });

        installButton.addEventListener('click', async () => {
            // Hide the app provided install promotion
            installButton.style.display = 'none';
            // Show the install prompt
            deferredPrompt.prompt();
            // Wait for the user to respond to the prompt
            const { outcome } = await deferredPrompt.userChoice;
            console.log(`User response to the install prompt: ${outcome}`);
            // We've used the prompt, and can't use it again, throw it away
            deferredPrompt = null;
        });

        window.addEventListener('appinstalled', () => {
            // Hide the install button
            installButton.style.display = 'none';
            // Clear the deferredPrompt so it can be garbage collected
            deferredPrompt = null;
            // Update the UI to show the app is installed
            pwaStatus.textContent = 'Installed';
            pwaStatus.className = 'badge bg-success';
        });

        // Check if the app is already installed
        if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone) {
            pwaStatus.textContent = 'Installed';
            pwaStatus.className = 'badge bg-success';
            installButton.style.display = 'none';
        } else {
            pwaStatus.textContent = 'Not Installed';
            pwaStatus.className = 'badge bg-secondary';
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
            const cache = await caches.open('library-manuals-v1.0.5'); // Ensure correct cache name
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

    async function clearCache() {
        if (confirm('Are you sure you want to clear all cached PDF files?')) {
            const btn = document.getElementById('clear-cache-btn');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Clearing...';
            btn.disabled = true;

            try {
                await caches.delete('library-manuals-v1.0.5'); // Ensure correct cache name
                showNotification('success', 'Cached files cleared successfully!');
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
