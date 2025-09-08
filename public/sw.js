'use strict';

// Note: The PWA installation prompt (beforeinstallprompt event) is handled by client-side
// JavaScript in the main application layout, not in the service worker. This file's
// responsibility is to manage caching, offline functionality, and background tasks.

// Cache names
const CACHE_VERSION = 'v1.0.9'; // Incremented version
const STATIC_CACHE_NAME = `library-static-${CACHE_VERSION}`;
const DYNAMIC_CACHE_NAME = `library-dynamic-${CACHE_VERSION}`;
const OFFLINE_URL = '/offline.html';

// Static assets to cache immediately
const STATIC_ASSETS = [
    OFFLINE_URL,
    '/',
    '/home',
    '/manuals',
    '/pwa-status',
    // CSS
    '/storage/assets/vendor/css/rtl/core.css',
    '/storage/assets/vendor/css/rtl/theme-default.css',
    '/storage/assets/vendor/css/pages/front-page.css',
    '/storage/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css',
    '/storage/assets/vendor/libs/typeahead-js/typeahead.css',
    '/storage/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css',
    '/storage/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css',
    '/storage/assets/vendor/libs/flatpickr/flatpickr.css',
    // JS
    '/storage/assets/vendor/libs/jquery/jquery.js',
    '/storage/assets/vendor/libs/popper/popper.js',
    '/storage/assets/vendor/js/bootstrap.js',
    '/storage/assets/vendor/js/helpers.js',
    '/storage/assets/vendor/js/template-customizer.js',
    '/storage/assets/vendor/js/menu.js',
    '/storage/assets/js/config.js',
    '/storage/assets/js/main.js',
    '/js/pdf.min.js',
    '/js/pdf.worker.min.js',
    // Fonts & JSON
    '/storage/assets/vendor/fonts/materialdesignicons/materialdesignicons-webfont.woff2?v=7.4.47',
    '/storage/assets/vendor/fonts/materialdesignicons/materialdesignicons-webfont.woff?v=7.4.47',
    '/storage/assets/vendor/fonts/materialdesignicons/materialdesignicons-webfont.ttf?v=7.4.47',
    '/storage/assets/json/locales/en.json',
    // Images & Icons
    '/logo.png',
    '/logo-144x144.png',
    '/logo-192x192.png',
    '/favicon.ico',
    // Manifest
    '/manifest.json'
];

// Install event - cache static assets
self.addEventListener('install', event => {
    console.log('[ServiceWorker] Installing...');
    event.waitUntil(
        caches.open(STATIC_CACHE_NAME)
            .then(cache => {
                console.log('[ServiceWorker] Caching static assets');
                return cache.addAll(STATIC_ASSETS);
            })
            .catch(error => {
                console.error('[ServiceWorker] Failed to cache static assets:', error);
            })
    );
    self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    console.log('[ServiceWorker] Activating...');
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== STATIC_CACHE_NAME && cacheName !== DYNAMIC_CACHE_NAME) {
                        console.log('[ServiceWorker] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// Fetch event - handle requests
self.addEventListener('fetch', event => {
    const {request} = event;
    const url = new URL(request.url);

    if (request.method !== 'GET') return;

    // PDFs: Online only. Go directly to the network.
    if (url.pathname.includes('/raw')) {
        event.respondWith(fetch(request));
        return;
    }

    // Static assets: Cache first, then network
    if (STATIC_ASSETS.some(asset => url.pathname.endsWith(asset.split('?')[0]))) {
        event.respondWith(cacheFirst(request, STATIC_CACHE_NAME));
    // Other requests: Network first, then cache
    } else {
        event.respondWith(networkFirst(request, DYNAMIC_CACHE_NAME));
    }
});

async function cacheFirst(request, cacheName) {
    const cachedResponse = await caches.match(request);
    return cachedResponse || fetch(request);
}

async function networkFirst(request, cacheName) {
    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            const cache = await caches.open(cacheName);
            self.waitUntil(cache.put(request, networkResponse.clone()));
        }
        return networkResponse;
    } catch (error) {
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }

        if (request.destination === 'document') {
            const offlinePage = await caches.match(OFFLINE_URL);
            if (offlinePage) {
                return offlinePage;
            }
        }

        return new Response('Network error: Resource not available offline.', {
            status: 503,
            statusText: 'Service Unavailable',
            headers: { 'Content-Type': 'text/plain' }
        });
    }
}

// Remove message listeners for caching PDFs
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
