"use strict";

// Cache names
const CACHE_VERSION = 'v1.0.1'; // Incremented version
const STATIC_CACHE_NAME = `library-static-${CACHE_VERSION}`;
const DYNAMIC_CACHE_NAME = `library-dynamic-${CACHE_VERSION}`;
const MANUALS_CACHE_NAME = `library-manuals-${CACHE_VERSION}`;
const OFFLINE_URL = '/offline.html';

// Static assets to cache immediately
const STATIC_ASSETS = [
    OFFLINE_URL,
    '/js/pdf.min.js', // Assuming pdf.js is stored locally
    '/js/pdf.worker.min.js'
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

    // Only handle GET requests
    if (request.method !== 'GET') {
        return;
    }

    // For PDF files, use a stale-while-revalidate strategy
    if (url.pathname.endsWith('.pdf') || url.pathname.includes('/raw')) {
        event.respondWith(handlePdfRequest(request));
    } else if (STATIC_ASSETS.some(asset => url.pathname.endsWith(asset))) {
        event.respondWith(caches.match(request));
    } else {
        // For other requests, use a network-first strategy
        event.respondWith(
            fetch(request).catch(() => caches.match(request) || caches.match(OFFLINE_URL))
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
