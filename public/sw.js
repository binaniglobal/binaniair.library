'use strict';

// Cache names
const CACHE_VERSION = 'v1.0.3'; // Incremented version
const STATIC_CACHE_NAME = `library-static-${CACHE_VERSION}`;
const DYNAMIC_CACHE_NAME = `library-dynamic-${CACHE_VERSION}`;
const MANUALS_CACHE_NAME = `library-manuals-${CACHE_VERSION}`;
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
                    if (cacheName !== STATIC_CACHE_NAME &&
                        cacheName !== DYNAMIC_CACHE_NAME &&
                        cacheName !== MANUALS_CACHE_NAME) {
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
    const { request } = event;
    const url = new URL(request.url);

    if (request.method !== 'GET') return;

    // PDFs: Stale-while-revalidate
    if (url.pathname.includes('/raw')) {
        event.respondWith(handlePdfRequest(request));
    // Static assets: Cache first, then network
    } else if (STATIC_ASSETS.some(asset => url.pathname.endsWith(asset))) {
        event.respondWith(
            caches.match(request).then(cachedResponse => {
                return cachedResponse || fetch(request);
            })
        );
    // Other requests: Network first, then cache, then offline page
    } else {
        event.respondWith(
            fetch(request)
                .then(networkResponse => {
                    if (networkResponse.ok) {
                        // Use waitUntil to not block the response while caching
                        event.waitUntil(
                            caches.open(DYNAMIC_CACHE_NAME).then(cache => {
                                cache.put(request, networkResponse.clone());
                            })
                        );
                    }
                    return networkResponse;
                })
                .catch(async () => { // Use an async function for proper awaiting
                    const cachedResponse = await caches.match(request);
                    return cachedResponse || await caches.match(OFFLINE_URL);
                })
        );
    }
});

// Stale-while-revalidate for PDFs
async function handlePdfRequest(request) {
    const cache = await caches.open(MANUALS_CACHE_NAME);
    const cachedResponse = await cache.match(request);

    const fetchPromise = fetch(request).then(networkResponse => {
        if (networkResponse.ok) {
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    });

    return cachedResponse || fetchPromise;
}

// Listen for messages from the client
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'CACHE_PDF') {
        const { raw_url, pwa_url } = event.data.payload;
        event.waitUntil(cachePdf(raw_url, pwa_url));
    }
});

// Function to fetch from PWA URL and cache against Raw URL
async function cachePdf(rawUrl, pwaUrl) {
    if (!rawUrl || !pwaUrl) {
        console.error('[ServiceWorker] Both raw_url and pwa_url are required for caching.');
        return;
    }

    console.log(`[ServiceWorker] Caching PDF. Key: ${rawUrl}, Source: ${pwaUrl}`);

    try {
        const response = await fetch(pwaUrl, { credentials: 'include' });

        if (response.ok) {
            const cache = await caches.open(MANUALS_CACHE_NAME);
            await cache.put(rawUrl, response.clone());
            console.log(`[ServiceWorker] Successfully cached PDF: ${rawUrl}`);
        } else {
            console.error(`[ServiceWorker] Failed to fetch PDF from ${pwaUrl}. Status: ${response.status}`);
        }
    } catch (error) {
        console.error(`[ServiceWorker] Error caching PDF ${pwaUrl}:`, error);
    }
}
