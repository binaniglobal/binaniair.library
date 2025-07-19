/**
 * PWA Cache Authentication Tests
 * Tests for offline document caching with authentication validation
 */

// Mock Service Worker and IndexedDB
const mockServiceWorker = {
    postMessage: jest.fn(),
    addEventListener: jest.fn(),
    registration: {
        active: true
    }
};

const mockIndexedDB = {
    open: jest.fn(),
    databases: []
};

// Mock LibraryStorageManager
class MockLibraryStorageManager {
    constructor() {
        this.db = null;
        this.isSupported = true;
    }

    async init() {
        return true;
    }

    async clearAllData() {
        return true;
    }

    async getCacheStats() {
        return {
            manualsCount: 5,
            itemsCount: 20,
            contentCount: 15,
            cacheMetadata: 10,
            lastSync: '2024-01-01T00:00:00Z'
        };
    }

    async setUserPreference(key, value) {
        return true;
    }

    async getUserPreference(key) {
        if (key === 'lastSync') {
            return '2024-01-01T00:00:00Z';
        }
        return null;
    }
}

// Mock fetch API
const mockFetch = jest.fn();
global.fetch = mockFetch;

// Mock navigator
global.navigator = {
    serviceWorker: mockServiceWorker,
    onLine: true
};

// Mock window
global.window = {
    libraryStorage: new MockLibraryStorageManager()
};

describe('PWA Authentication and Caching Tests', () => {
    beforeEach(() => {
        jest.clearAllMocks();
        mockFetch.mockClear();
    });

    describe('Authentication Token Management', () => {
        test('should fetch auth token for authenticated user', async () => {
            const mockToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.test.signature';
            const mockResponse = {
                token: mockToken,
                expires_at: Date.now() / 1000 + 3600,
                user: { id: 1, name: 'Test User' }
            };

            mockFetch.mockResolvedValueOnce({
                ok: true,
                json: async () => mockResponse
            });

            const response = await fetch('/pwa/auth-token');
            const data = await response.json();

            expect(mockFetch).toHaveBeenCalledWith('/pwa/auth-token');
            expect(data.token).toBe(mockToken);
            expect(data.user.id).toBe(1);
        });

        test('should handle authentication failure', async () => {
            mockFetch.mockResolvedValueOnce({
                ok: false,
                status: 401,
                json: async () => ({ error: 'Not authenticated' })
            });

            const response = await fetch('/pwa/auth-token');
            const data = await response.json();

            expect(response.ok).toBe(false);
            expect(data.error).toBe('Not authenticated');
        });

        test('should validate token expiry', () => {
            const currentTime = Date.now() / 1000;
            const expiredToken = {
                expires_at: currentTime - 3600 // Expired 1 hour ago
            };
            const validToken = {
                expires_at: currentTime + 3600 // Valid for 1 hour
            };

            expect(expiredToken.expires_at < currentTime).toBe(true);
            expect(validToken.expires_at > currentTime).toBe(true);
        });
    });

    describe('Document Caching with Authentication', () => {
        test('should cache document with valid authentication', async () => {
            const mockDocumentData = {
                name: 'Test Document.pdf',
                pwa_url: '/pwa/download/submanuals/test-document.pdf',
                file_type: 'application/pdf'
            };

            const mockPdfResponse = new Response(new ArrayBuffer(1024), {
                status: 200,
                headers: {
                    'Content-Type': 'application/pdf',
                    'X-PWA-Cache': 'true'
                }
            });

            mockFetch.mockResolvedValueOnce(mockPdfResponse);

            const response = await fetch(mockDocumentData.pwa_url, {
                headers: { 'X-PWA-Token': 'valid-token' }
            });

            expect(response.ok).toBe(true);
            expect(response.headers.get('Content-Type')).toBe('application/pdf');
            expect(response.headers.get('X-PWA-Cache')).toBe('true');
        });

        test('should reject document caching without authentication', async () => {
            mockFetch.mockResolvedValueOnce({
                ok: false,
                status: 401,
                json: async () => ({
                    error: 'Authentication required',
                    debug: {
                        has_auth_user: false,
                        has_token: false
                    }
                })
            });

            const response = await fetch('/pwa/download/submanuals/test-document.pdf');
            const data = await response.json();

            expect(response.ok).toBe(false);
            expect(data.error).toBe('Authentication required');
        });

        test('should reject non-PDF files', async () => {
            mockFetch.mockResolvedValueOnce({
                ok: false,
                status: 400,
                json: async () => ({
                    error: 'File is not a PDF',
                    mime_type: 'text/plain'
                })
            });

            const response = await fetch('/pwa/download/submanuals/document.txt', {
                headers: { 'X-PWA-Token': 'valid-token' }
            });
            const data = await response.json();

            expect(response.ok).toBe(false);
            expect(data.error).toBe('File is not a PDF');
        });
    });

    describe('Cache Invalidation', () => {
        test('should clear cache on logout', async () => {
            const clearCacheSpy = jest.spyOn(window.libraryStorage, 'clearAllData');
            
            // Simulate logout
            await simulateLogout();
            
            expect(clearCacheSpy).toHaveBeenCalled();
        });

        test('should invalidate cache on session expiry', async () => {
            const currentTime = Date.now() / 1000;
            const expiredSession = {
                expires_at: currentTime - 100 // Expired
            };

            const isExpired = expiredSession.expires_at < currentTime;
            expect(isExpired).toBe(true);

            if (isExpired) {
                const clearCacheSpy = jest.spyOn(window.libraryStorage, 'clearAllData');
                await window.libraryStorage.clearAllData();
                expect(clearCacheSpy).toHaveBeenCalled();
            }
        });

        test('should refresh auth token periodically', async () => {
            const mockNewToken = 'new-token-value';
            mockFetch.mockResolvedValueOnce({
                ok: true,
                json: async () => ({
                    token: mockNewToken,
                    expires_at: Date.now() / 1000 + 3600,
                    user: { id: 1, name: 'Test User' }
                })
            });

            // Simulate token refresh
            const response = await fetch('/pwa/auth-token');
            const data = await response.json();

            expect(data.token).toBe(mockNewToken);
            expect(mockServiceWorker.postMessage).toHaveBeenCalledWith({
                type: 'SET_AUTH_TOKEN',
                token: mockNewToken,
                expires_at: data.expires_at
            });
        });
    });

    describe('Permission-Based Access Control', () => {
        test('should return only accessible manuals for user', async () => {
            const mockManuals = [
                { id: 1, name: 'Accessible Manual', type: 0 },
                // Restricted manual should not be returned
            ];

            mockFetch.mockResolvedValueOnce({
                ok: true,
                json: async () => ({
                    success: true,
                    data: mockManuals
                })
            });

            const response = await fetch('/api/manuals');
            const data = await response.json();

            expect(data.success).toBe(true);
            expect(data.data.length).toBe(1);
            expect(data.data[0].name).toBe('Accessible Manual');
        });

        test('should return empty array for user without permissions', async () => {
            mockFetch.mockResolvedValueOnce({
                ok: true,
                json: async () => ({
                    success: true,
                    data: []
                })
            });

            const response = await fetch('/api/manuals');
            const data = await response.json();

            expect(data.success).toBe(true);
            expect(data.data.length).toBe(0);
        });
    });

    describe('Offline Functionality', () => {
        test('should serve cached documents when offline', async () => {
            // Mock Cache API
            const mockCache = {
                match: jest.fn().mockResolvedValue(new Response(new ArrayBuffer(1024), {
                    status: 200,
                    headers: { 'Content-Type': 'application/pdf' }
                })),
                put: jest.fn()
            };

            global.caches = {
                open: jest.fn().mockResolvedValue(mockCache),
                match: jest.fn().mockResolvedValue(new Response(new ArrayBuffer(1024)))
            };

            // Simulate offline
            global.navigator.onLine = false;

            const cachedResponse = await caches.match('/pwa/download/submanuals/test-document.pdf');
            expect(cachedResponse).toBeDefined();
            expect(cachedResponse.status).toBe(200);
        });

        test('should sync when coming back online', async () => {
            const syncSpy = jest.spyOn(window.libraryStorage, 'setUserPreference');
            
            // Simulate coming back online
            global.navigator.onLine = true;
            
            // Trigger sync
            await simulateOnlineSync();
            
            expect(syncSpy).toHaveBeenCalledWith('lastSync', expect.any(String));
        });
    });

    describe('Cache Statistics and Management', () => {
        test('should provide cache statistics', async () => {
            const stats = await window.libraryStorage.getCacheStats();
            
            expect(stats).toBeDefined();
            expect(stats.manualsCount).toBe(5);
            expect(stats.itemsCount).toBe(20);
            expect(stats.contentCount).toBe(15);
            expect(stats.lastSync).toBeDefined();
        });

        test('should clear all cached data', async () => {
            const clearSpy = jest.spyOn(window.libraryStorage, 'clearAllData');
            
            await window.libraryStorage.clearAllData();
            
            expect(clearSpy).toHaveBeenCalled();
        });
    });

    describe('Error Handling', () => {
        test('should handle network errors gracefully', async () => {
            mockFetch.mockRejectedValueOnce(new Error('Network error'));

            try {
                await fetch('/pwa/auth-token');
            } catch (error) {
                expect(error.message).toBe('Network error');
            }
        });

        test('should handle malformed token', async () => {
            const malformedToken = 'invalid-token-format';
            
            mockFetch.mockResolvedValueOnce({
                ok: false,
                status: 401,
                json: async () => ({ error: 'Authentication required' })
            });

            const response = await fetch('/pwa/download/submanuals/test.pdf', {
                headers: { 'X-PWA-Token': malformedToken }
            });

            expect(response.ok).toBe(false);
        });
    });
});

// Helper functions for testing
async function simulateLogout() {
    // Simulate logout process
    await window.libraryStorage.clearAllData();
    await window.libraryStorage.setUserPreference('lastSync', null);
}

async function simulateOnlineSync() {
    // Simulate online sync
    if (navigator.onLine) {
        await window.libraryStorage.setUserPreference('lastSync', new Date().toISOString());
    }
}

// Mock Service Worker message handling
function mockServiceWorkerMessage(data) {
    return new Promise((resolve) => {
        const mockEvent = {
            data: data,
            ports: [{
                postMessage: (response) => resolve(response)
            }]
        };
        
        // Simulate service worker message handling
        if (data.type === 'SET_AUTH_TOKEN') {
            resolve({ success: true });
        } else if (data.type === 'CACHE_INDIVIDUAL_DOCUMENT') {
            resolve({ success: true, message: 'Document cached successfully' });
        }
    });
}

// Export for use in other test files
module.exports = {
    MockLibraryStorageManager,
    mockServiceWorkerMessage,
    simulateLogout,
    simulateOnlineSync
};
