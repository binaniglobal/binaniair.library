"use strict";

// Cache names
const CACHE_VERSION = 'v1.0.0';
const STATIC_CACHE_NAME = `library-static-${CACHE_VERSION}`;
const DYNAMIC_CACHE_NAME = `library-dynamic-${CACHE_VERSION}`;
const IMAGES_CACHE_NAME = `library-images-${CACHE_VERSION}`;
const MANUALS_CACHE_NAME = `library-manuals-${CACHE_VERSION}`;
const OFFLINE_URL = '/offline.html';

// Static assets to cache immediately
const STATIC_ASSETS = [
    OFFLINE_URL,
    '/',
    '/manuals',
    '/home',
    '/storage/assets/vendor/css/rtl/core.css',
    '/storage/assets/vendor/css/rtl/theme-default.css',
    '/storage/assets/vendor/css/pages/front-page.css',
    '/storage/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css',
    '/storage/assets/vendor/libs/typeahead-js/typeahead.css',
    '/storage/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css',
    '/storage/assets/vendor/js/helpers.js',
    '/storage/assets/vendor/js/template-customizer.js',
    '/storage/assets/js/config.js',
    '/storage/assets/vendor/js/bootstrap.js',
    '/storage/assets/vendor/libs/jquery/jquery.js',
    '/storage/assets/vendor/libs/popper/popper.js',
    '/storage/assets/vendor/js/menu.js',
    '/storage/assets/js/main.js'
];

// Install event - cache static assets
self.addEventListener('install', event => {
    console.log('[ServiceWorker] Installing...');
    
    event.waitUntil(
        Promise.all([
            caches.open(STATIC_CACHE_NAME)
                .then(cache => {
                    console.log('[ServiceWorker] Caching static assets');
                    return cache.addAll(STATIC_ASSETS.map(url => new Request(url, { credentials: 'same-origin' })));
                })
                .catch(error => {
                    console.error('[ServiceWorker] Failed to cache static assets:', error);
                    // Continue with installation even if some assets fail
                    return Promise.resolve();
                })
        ])
    );
    
    // Force the waiting service worker to become the active service worker
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
                        cacheName !== IMAGES_CACHE_NAME &&
                        cacheName !== MANUALS_CACHE_NAME) {
                        console.log('[ServiceWorker] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    
    // Take control of all pages immediately
    self.clients.claim();
});

// Fetch event - implement caching strategies
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);
    
    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }
    
// Skip requests to different origins (except for allowed CDNs)
    if (url.origin !== self.location.origin && !isAllowedCDN(url.origin)) {
        return;
    }
    
    // In development, add more lenient handling
    if (url.hostname === '127.0.0.1' || url.hostname === 'localhost') {
        console.log('[ServiceWorker] Handling local development request:', request.url);
    }
    
    // Handle different types of requests
    if (isNavigationRequest(request)) {
        event.respondWith(handleNavigation(request));
    } else if (isStaticAsset(request)) {
        event.respondWith(handleStaticAsset(request));
    } else if (isImageRequest(request)) {
        event.respondWith(handleImageRequest(request));
    } else if (isManualFileRequest(request)) {
        event.respondWith(handleManualFileRequest(request));
    } else {
        event.respondWith(handleDynamicRequest(request));
    }
});

// Helper functions
function isNavigationRequest(request) {
    return request.mode === 'navigate';
}

function isStaticAsset(request) {
    const url = new URL(request.url);
    return url.pathname.includes('/storage/assets/') || 
           url.pathname.endsWith('.css') || 
           url.pathname.endsWith('.js') ||
           url.pathname.endsWith('.woff') ||
           url.pathname.endsWith('.woff2');
}

function isImageRequest(request) {
    const url = new URL(request.url);
    return url.pathname.includes('/storage/') && 
           (url.pathname.endsWith('.png') || 
            url.pathname.endsWith('.jpg') || 
            url.pathname.endsWith('.jpeg') || 
            url.pathname.endsWith('.gif') || 
            url.pathname.endsWith('.svg') ||
            url.pathname.endsWith('.webp'));
}

function isManualFileRequest(request) {
    const url = new URL(request.url);
    return (url.pathname.includes('/manual/sub-manuals/file/') || 
            url.pathname.includes('/manual/sub-manuals/content/file/') ||
            url.pathname.includes('/pwa/download/') ||
            url.pathname.endsWith('.pdf') ||
            url.pathname.startsWith('/api/manual'));
}

function isAllowedCDN(origin) {
    const allowedCDNs = [
        'https://fonts.googleapis.com',
        'https://fonts.gstatic.com',
        'https://cdnjs.cloudflare.com',
        'https://cdn.datatables.net'
    ];
    return allowedCDNs.includes(origin);
}

// Navigation handler - Network first, then cache, then offline page
async function handleNavigation(request) {
    try {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 8000); // 8 second timeout
        
        const response = await fetch(request, {
            signal: controller.signal,
            credentials: 'same-origin'
        });
        
        clearTimeout(timeoutId);
        
        if (response.ok) {
            // Cache successful navigation responses
            const cache = await caches.open(DYNAMIC_CACHE_NAME);
            cache.put(request, response.clone());
        }
        
        return response;
    } catch (error) {
        console.log('[ServiceWorker] Network failed for navigation:', error.message);
        
        // In development, be more lenient with errors
        if (request.url.includes('127.0.0.1') || request.url.includes('localhost')) {
            console.log('[ServiceWorker] Development environment - allowing fallback to cache or offline page');
        }
        
        // Try to get from cache
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            console.log('[ServiceWorker] Serving cached navigation for:', request.url);
            return cachedResponse;
        }
        
        // Return offline page as last resort
        console.log('[ServiceWorker] Serving offline page');
        return caches.match(OFFLINE_URL);
    }
}

// Static assets handler - Cache first strategy
async function handleStaticAsset(request) {
    const cachedResponse = await caches.match(request);
    
    if (cachedResponse) {
        return cachedResponse;
    }
    
    try {
        const response = await fetch(request);
        
        if (response.ok) {
            const cache = await caches.open(STATIC_CACHE_NAME);
            cache.put(request, response.clone());
        }
        
        return response;
    } catch (error) {
        console.error('[ServiceWorker] Failed to fetch static asset:', request.url);
        throw error;
    }
}

// Images handler - Cache first with long-term storage
async function handleImageRequest(request) {
    const cachedResponse = await caches.match(request);
    
    if (cachedResponse) {
        return cachedResponse;
    }
    
    try {
        const response = await fetch(request);
        
        if (response.ok) {
            const cache = await caches.open(IMAGES_CACHE_NAME);
            cache.put(request, response.clone());
        }
        
        return response;
    } catch (error) {
        console.log('[ServiceWorker] Failed to fetch image:', request.url);
        // Return a placeholder image or let it fail gracefully
        throw error;
    }
}

// Manual files handler - Cache first for offline access
async function handleManualFileRequest(request) {
    const cachedResponse = await caches.match(request);
    
    if (cachedResponse) {
        console.log('[ServiceWorker] Serving from cache:', request.url);
        return cachedResponse;
    }
    
    try {
        const response = await fetch(request, { credentials: 'same-origin' });
        
        if (response.ok) {
            const cache = await caches.open(MANUALS_CACHE_NAME);
            // Only cache successful responses
            if (response.status === 200) {
                cache.put(request, response.clone());
                console.log('[ServiceWorker] Cached manual file:', request.url);
            }
        }
        
        return response;
    } catch (error) {
        console.log('[ServiceWorker] Failed to fetch manual file:', request.url);
        
        // Try to serve from any cache as fallback
        const fallbackResponse = await caches.match(request);
        if (fallbackResponse) {
            console.log('[ServiceWorker] Serving fallback from cache:', request.url);
            return fallbackResponse;
        }
        
        throw error;
    }
}

// Dynamic content handler - Network first, then cache
async function handleDynamicRequest(request) {
    try {
        // Add timeout to prevent hanging requests in dev
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout
        
        const response = await fetch(request, {
            signal: controller.signal,
            credentials: 'same-origin'
        });
        
        clearTimeout(timeoutId);
        
        if (response.ok) {
            const cache = await caches.open(DYNAMIC_CACHE_NAME);
            cache.put(request, response.clone());
        }
        
        return response;
    } catch (error) {
        console.log('[ServiceWorker] Network failed for dynamic request:', error.message);
        
        // In development, don't treat all errors as offline
        if (error.name === 'AbortError') {
            console.log('[ServiceWorker] Request timed out, treating as network error');
        }
        
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            console.log('[ServiceWorker] Serving from cache for:', request.url);
            return cachedResponse;
        }
        
        // For local development, pass through the error instead of treating as offline
        if (request.url.includes('127.0.0.1') || request.url.includes('localhost')) {
            console.log('[ServiceWorker] Development environment - passing through error');
            return new Response('Network Error in Development', { status: 503 });
        }
        
        throw error;
    }
}

// Global variables for authentication
let authToken = null;
let authTokenExpiry = null;

// Listen for messages from the main thread
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
        return;
    }
    
    if (event.data && event.data.type === 'SET_AUTH_TOKEN') {
        authToken = event.data.token;
        authTokenExpiry = event.data.expires_at;
        console.log('[ServiceWorker] Authentication token updated');
        return;
    }
    
    if (event.data && event.data.type === 'CACHE_MANUAL') {
        event.waitUntil(
            waitForAuthTokenAndCache(event.data.manuals)
                .then(() => {
                    // Send success response back to the main thread
                    if (event.ports && event.ports[0]) {
                        event.ports[0].postMessage({
                            success: true,
                            message: 'Manual caching completed'
                        });
                    }
                })
                .catch(error => {
                    console.error('[ServiceWorker] Manual caching failed:', error);
                    // Send error response back to the main thread
                    if (event.ports && event.ports[0]) {
                        event.ports[0].postMessage({
                            success: false,
                            error: error.message
                        });
                    }
                })
        );
        return;
    }
    
    if (event.data && event.data.type === 'CACHE_INDIVIDUAL_DOCUMENT') {
        event.waitUntil(
            cacheIndividualDocument(event.data.document)
                .then(() => {
                    // Send success response back to the main thread
                    if (event.ports && event.ports[0]) {
                        event.ports[0].postMessage({
                            success: true,
                            message: 'Document cached successfully'
                        });
                    }
                })
                .catch(error => {
                    console.error('[ServiceWorker] Individual document caching failed:', error);
                    // Send error response back to the main thread
                    if (event.ports && event.ports[0]) {
                        event.ports[0].postMessage({
                            success: false,
                            error: error.message
                        });
                    }
                })
        );
        return;
    }
});

// Wait for auth token and then cache
async function waitForAuthTokenAndCache(manuals) {
    console.log('[ServiceWorker] Waiting for auth token before caching...');
    
    // Wait up to 5 seconds for auth token
    let attempts = 0;
    const maxAttempts = 50; // 50 attempts * 100ms = 5 seconds
    
    while (attempts < maxAttempts && !authToken) {
        await new Promise(resolve => setTimeout(resolve, 100));
        attempts++;
    }
    
    if (!authToken) {
        console.warn('[ServiceWorker] No auth token available after waiting, proceeding anyway...');
    } else {
        console.log('[ServiceWorker] Auth token available, proceeding with caching');
    }
    
    return cacheManualData(manuals);
}

// Cache manual data for offline access
async function cacheManualData(manuals) {
    if (!authToken || (authTokenExpiry && authTokenExpiry < Date.now() / 1000)) {
        console.error('[ServiceWorker] Unable to cache: invalid or expired auth token');
        return;
    }
    try {
        const cache = await caches.open(DYNAMIC_CACHE_NAME);
        
        // Cache manual pages and their associated data
        for (const manual of manuals) {
            const manualUrl = `/manual/sub-manuals/${manual.id}`;
            try {
                // Cache the manual page
                const response = await fetch(manualUrl);
                if (response.ok) {
                    await cache.put(manualUrl, response.clone());
                }
                
                // Cache the manual items API data
                const apiResponse = await fetch(`/api/manual/${manual.id}/items`);
                if (apiResponse.ok) {
                    await cache.put(`/api/manual/${manual.id}/items`, apiResponse.clone());
                    
                    // Parse the response to get items and cache their files
                    const data = await apiResponse.json();
                    if (data.success && data.data) {
                        await cacheManualItems(data.data);
                    }
                }
            } catch (error) {
                console.warn('[ServiceWorker] Failed to cache manual:', manual.id, error);
            }
        }
    } catch (error) {
        console.error('[ServiceWorker] Failed to cache manual data:', error);
    }
}

// Cache manual items and their files
async function cacheManualItems(items) {
    const cache = await caches.open(MANUALS_CACHE_NAME);
    let cachedFilesCount = 0;
    
    for (const item of items) {
        try {
            console.log('[ServiceWorker] Processing item:', item.name, 'Type:', item.file_type);
            
            // Cache PDF files using PWA URLs ONLY
            if (item.file_type === 'application/pdf') {
                console.log('[ServiceWorker] Processing PDF:', item.name);
                console.log('[ServiceWorker] PWA URL:', item.pwa_url);
                console.log('[ServiceWorker] File path:', item.file_path);
                
                if (item.pwa_url) {
                    try {
                        console.log('[ServiceWorker] Attempting to fetch PWA URL:', item.pwa_url);
                        console.log('[ServiceWorker] Item details:', {
                            name: item.name,
                            file_path: item.file_path,
                            file_type: item.file_type,
                            pwa_url: item.pwa_url
                        });
                        
                        // Validate URL before fetching
                        if (!item.pwa_url) {
                            console.error('[ServiceWorker] No PWA URL available for item:', item.name);
                            continue;
                        }
                        
                        if (!item.pwa_url.startsWith('/pwa/download/')) {
                            console.error('[ServiceWorker] Invalid PWA URL format (should start with /pwa/download/):', item.pwa_url, 'for item:', item.name);
                            continue;
                        }
                        
                        // Prepare headers with authentication if available
                        const headers = {
                            'Accept': 'application/pdf,*/*',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Cache-Control': 'no-cache'
                        };
                        
                        // Add authentication token if available and not expired
                        if (authToken && (!authTokenExpiry || authTokenExpiry > Date.now() / 1000)) {
                            headers['X-PWA-Token'] = authToken;
                            console.log('[ServiceWorker] Using PWA authentication token for:', item.name);
                        } else {
                            console.log('[ServiceWorker] No valid authentication token available for:', item.name);
                        }
                        
                        // Special handling for local development
                        const isLocalDev = self.location.hostname === '127.0.0.10' || 
                                          self.location.hostname === 'localhost' || 
                                          self.location.hostname === '127.0.0.1';
                        
const fetchOptions = {
                            method: 'GET',
                            credentials: 'same-origin',
                            cache: 'no-cache',
                            headers
                        };
                        
                        // In local development, use 'cors' mode to avoid same-origin issues
                        if (isLocalDev) {
                            fetchOptions.mode = 'cors';
                            console.log('[ServiceWorker] Using CORS mode for local development');
                        } else {
                            fetchOptions.mode = 'same-origin';
                        }
                        
                        const fileResponse = await fetch(item.pwa_url, fetchOptions);
                        
                        console.log('[ServiceWorker] Fetch response status:', fileResponse.status, 'for', item.name);
                        console.log('[ServiceWorker] Response headers:', Object.fromEntries(fileResponse.headers.entries()));
                        
                        console.log('[ServiceWorker] Full response details:', {
                            status: fileResponse.status,
                            statusText: fileResponse.statusText,
                            headers: Object.fromEntries(fileResponse.headers.entries()),
                            url: fileResponse.url,
                            redirected: fileResponse.redirected
                        });
                        
                        if (fileResponse.ok) {
                            // Verify it's actually a PDF
                            const contentType = fileResponse.headers.get('content-type');
                            console.log('[ServiceWorker] Content-Type:', contentType);
                            
                            if (contentType && (contentType.includes('pdf') || contentType.includes('application/pdf'))) {
                                // Only cache using PWA URL
                                await cache.put(item.pwa_url, fileResponse.clone());
                                console.log('[ServiceWorker] Successfully cached PDF file:', item.name);
                                cachedFilesCount++;
                            } else {
                                console.warn('[ServiceWorker] Response is not a PDF for:', item.name, 'Content-Type:', contentType);
                                // Log response text for debugging if it's small
                                const contentLength = fileResponse.headers.get('content-length');
                                if (!contentLength || parseInt(contentLength) < 1000) {
                                    try {
                                        const responseText = await fileResponse.clone().text();
                                        console.warn('[ServiceWorker] Response body:', responseText.substring(0, 500));
                                        
                                        // Check if it's an authentication error
                                        if (responseText.includes('Authentication required') || responseText.includes('error')) {
                                            console.error('[ServiceWorker] Authentication error detected for:', item.name);
                                            // Try to refresh the auth token
                                            console.log('[ServiceWorker] Attempting to refresh auth token...');
                                        }
                                    } catch (textError) {
                                        console.error('[ServiceWorker] Could not read response text:', textError);
                                    }
                                }
                            }
                        } else {
                            console.error('[ServiceWorker] Failed to fetch PDF via PWA URL (status ' + fileResponse.status + '):', item.name, item.pwa_url);
                            // Log response text for debugging if it's an error
                            try {
                                const errorText = await fileResponse.clone().text();
                                console.error('[ServiceWorker] Error response:', errorText.substring(0, 500));
                            } catch (e) {
                                console.error('[ServiceWorker] Could not read error response');
                            }
                        }
                    } catch (fetchError) {
                        console.error(`[ServiceWorker] Network error fetching PDF "${item.name}" via PWA URL:`, fetchError.message);
                        console.error('[ServiceWorker] Failed URL:', item.pwa_url);
                        console.error('[ServiceWorker] Full error details:', {
                            name: fetchError.name,
                            message: fetchError.message,
                            stack: fetchError.stack?.substring(0, 500) // Limit stack trace
                        });
                        
                        // Log specific error types for debugging
                        if (fetchError.name === 'TypeError' && fetchError.message.includes('Failed to fetch')) {
                            console.error('[ServiceWorker] This is likely a network or CORS error. Check if the server is running and the URL is accessible.');
                        }
                    }
                } else {
                    console.error('[ServiceWorker] No PWA URL available for PDF file:', item.name);
                }
            }
            
            // If it's a folder, cache its content
            if (item.file_type === 'Folder' && item.url) {
                const folderResponse = await fetch(item.url);
                if (folderResponse.ok) {
                    await cache.put(item.url, folderResponse.clone());
                    
                    // Cache folder content API
                    const contentApiUrl = `/api/manual-item/${item.id}/content`;
                    const contentApiResponse = await fetch(contentApiUrl);
                    if (contentApiResponse.ok) {
                        await cache.put(contentApiUrl, contentApiResponse.clone());
                        
                        // Parse and cache individual content files
                        const contentData = await contentApiResponse.json();
                        if (contentData.success && contentData.data) {
                            const folderCachedCount = await cacheManualContent(contentData.data);
                            cachedFilesCount += folderCachedCount;
                        }
                    }
                }
            }
        } catch (error) {
            console.warn('[ServiceWorker] Failed to cache item:', item.name, error);
        }
    }
    
    return cachedFilesCount;
}

// Cache manual content files using PWA URLs ONLY
async function cacheManualContent(contents) {
    const cache = await caches.open(MANUALS_CACHE_NAME);
    let cachedFilesCount = 0;
    
    for (const content of contents) {
        try {
            // Use only PWA URL for caching
            if (content.pwa_url) {
                // Prepare headers with authentication if available
                const headers = {
                    'Accept': 'application/pdf,*/*',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache'
                };
                
                // Add authentication token if available and not expired
                if (authToken && (!authTokenExpiry || authTokenExpiry > Date.now() / 1000)) {
                    headers['X-PWA-Token'] = authToken;
                    console.log('[ServiceWorker] Using PWA authentication token for content:', content.name);
                } else {
                    console.log('[ServiceWorker] No valid authentication token available for content:', content.name);
                }
                
                const contentResponse = await fetch(content.pwa_url, { 
                    method: 'GET',
                    credentials: 'include',
                    mode: 'same-origin',
                    cache: 'no-cache',
                    headers
                });
                
                if (contentResponse.ok) {
                    // Verify it's actually a PDF
                    const contentType = contentResponse.headers.get('content-type');
                    if (contentType && (contentType.includes('pdf') || contentType.includes('application/pdf'))) {
                        await cache.put(content.pwa_url, contentResponse.clone());
                        console.log('[ServiceWorker] Cached content:', content.name);
                        cachedFilesCount++;
                    } else {
                        console.warn('[ServiceWorker] Content is not a PDF:', content.name, 'Content-Type:', contentType);
                    }
                } else {
                    console.error('[ServiceWorker] Failed to fetch content via PWA URL (status ' + contentResponse.status + '):', content.name);
                }
            } else {
                console.error('[ServiceWorker] No PWA URL available for content:', content.name);
            }
        } catch (error) {
            console.error('[ServiceWorker] Failed to cache content:', content.name, error.message);
        }
    }
    
    return cachedFilesCount;
}

// Cache individual document for offline access
async function cacheIndividualDocument(docData) {
    console.log('[ServiceWorker] Caching individual document:', docData.name);
    
    if (!authToken || (authTokenExpiry && authTokenExpiry < Date.now() / 1000)) {
        console.error('[ServiceWorker] Unable to cache document: invalid or expired auth token');
        throw new Error('Authentication token invalid or expired');
    }
    
    if (!docData.pwa_url) {
        console.error('[ServiceWorker] No PWA URL available for document:', docData.name);
        throw new Error('No PWA URL available for document');
    }
    
    try {
        const cache = await caches.open(MANUALS_CACHE_NAME);
        
        // Prepare headers with authentication
        const headers = {
            'Accept': 'application/pdf,*/*',
            'X-Requested-With': 'XMLHttpRequest',
            'Cache-Control': 'no-cache'
        };
        
        // Add authentication token
        if (authToken && (!authTokenExpiry || authTokenExpiry > Date.now() / 1000)) {
            headers['X-PWA-Token'] = authToken;
            console.log('[ServiceWorker] Using PWA authentication token for document:', docData.name);
        }
        
        // Special handling for local development
        const isLocalDev = self.location.hostname === '127.0.0.10' || 
                          self.location.hostname === 'localhost' || 
                          self.location.hostname === '127.0.0.1';
        
        const fetchOptions = {
            method: 'GET',
            credentials: 'same-origin',
            cache: 'no-cache',
            headers
        };
        
        // In local development, use 'cors' mode to avoid same-origin issues
        if (isLocalDev) {
            fetchOptions.mode = 'cors';
            console.log('[ServiceWorker] Using CORS mode for local development');
        } else {
            fetchOptions.mode = 'same-origin';
        }
        
        console.log('[ServiceWorker] Fetching individual document:', docData.pwa_url);
        const fileResponse = await fetch(docData.pwa_url, fetchOptions);
        
        console.log('[ServiceWorker] Individual document response status:', fileResponse.status);
        
        if (fileResponse.ok) {
            // Verify it's actually a PDF
            const contentType = fileResponse.headers.get('content-type');
            console.log('[ServiceWorker] Individual document Content-Type:', contentType);
            
            if (contentType && (contentType.includes('pdf') || contentType.includes('application/pdf'))) {
                // Cache the document
                await cache.put(docData.pwa_url, fileResponse.clone());
                console.log('[ServiceWorker] Successfully cached individual document:', docData.name);
                return { success: true, message: 'Document cached successfully' };
            } else {
                console.warn('[ServiceWorker] Individual document is not a PDF:', docData.name, 'Content-Type:', contentType);
                throw new Error('Document is not a PDF file');
            }
        } else {
            console.error('[ServiceWorker] Failed to fetch individual document (status ' + fileResponse.status + '):', docData.name);
            
            // Log response text for debugging if it's an error
            try {
                const errorText = await fileResponse.clone().text();
                console.error('[ServiceWorker] Individual document error response:', errorText.substring(0, 500));
            } catch (e) {
                console.error('[ServiceWorker] Could not read individual document error response');
            }
            
            throw new Error(`Failed to fetch document: HTTP ${fileResponse.status}`);
        }
    } catch (error) {
        console.error('[ServiceWorker] Individual document caching error:', error.message);
        throw error;
    }
}
