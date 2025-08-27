/**
 * BinaniAir Library - PWA Storage Management
 * Handles IndexedDB operations for offline manual data storage
 */

class LibraryStorageManager {
    constructor() {
        this.dbName = 'BinaniAirLibraryDB';
        this.dbVersion = 1;
        this.db = null;
        this.isSupported = this.checkIndexedDBSupport();
    }

    /**
     * Check if IndexedDB is supported
     */
    checkIndexedDBSupport() {
        return 'indexedDB' in window;
    }

    /**
     * Initialize the database
     */
    async init() {
        console.log('[LibraryStorage] Starting initialization...');
        
        if (!this.isSupported) {
            console.warn('[LibraryStorage] IndexedDB is not supported');
            return false;
        }

        return new Promise((resolve, reject) => {
            console.log('[LibraryStorage] Opening database:', this.dbName, 'version:', this.dbVersion);
            const request = indexedDB.open(this.dbName, this.dbVersion);

            request.onerror = () => {
                console.error('[LibraryStorage] Failed to open database:', request.error);
                reject(request.error);
            };

            request.onsuccess = () => {
                this.db = request.result;
                console.log('[LibraryStorage] Database opened successfully');
                console.log('[LibraryStorage] Object stores:', Array.from(this.db.objectStoreNames));
                resolve(true);
            };

            request.onupgradeneeded = (event) => {
                console.log('[LibraryStorage] Database upgrade needed');
                this.db = event.target.result;
                this.createObjectStores();
            };
        });
    }

    /**
     * Create object stores for different data types
     */
    createObjectStores() {
        console.log('[LibraryStorage] Creating object stores...');

        // Manuals store
        if (!this.db.objectStoreNames.contains('manuals')) {
            const manualsStore = this.db.createObjectStore('manuals', { keyPath: 'id' });
            manualsStore.createIndex('name', 'name', { unique: false });
            manualsStore.createIndex('type', 'type', { unique: false });
        }

        // Manual items store
        if (!this.db.objectStoreNames.contains('manualItems')) {
            const itemsStore = this.db.createObjectStore('manualItems', { keyPath: 'id' });
            itemsStore.createIndex('manual_uid', 'manual_uid', { unique: false });
            itemsStore.createIndex('name', 'name', { unique: false });
        }

        // Manual content store
        if (!this.db.objectStoreNames.contains('manualContent')) {
            const contentStore = this.db.createObjectStore('manualContent', { keyPath: 'id' });
            contentStore.createIndex('item_uid', 'item_uid', { unique: false });
            contentStore.createIndex('name', 'name', { unique: false });
        }

        // User preferences store
        if (!this.db.objectStoreNames.contains('userPreferences')) {
            this.db.createObjectStore('userPreferences', { keyPath: 'key' });
        }

        // Cache metadata store
        if (!this.db.objectStoreNames.contains('cacheMetadata')) {
            const metadataStore = this.db.createObjectStore('cacheMetadata', { keyPath: 'url' });
            metadataStore.createIndex('timestamp', 'timestamp', { unique: false });
            metadataStore.createIndex('type', 'type', { unique: false });
        }
    }

    /**
     * Store manual data
     */
    async storeManual(manual) {
        if (!this.db) return false;

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['manuals'], 'readwrite');
            const store = transaction.objectStore('manuals');
            
            const manualData = {
                id: manual.id || manual.mid,
                name: manual.name,
                type: manual.type || 0,
                cachedAt: new Date().toISOString(),
                ...manual
            };

            const request = store.put(manualData);

            request.onsuccess = () => {
                console.log('[LibraryStorage] Manual stored successfully:', manual.name);
                resolve(true);
            };

            request.onerror = () => {
                console.error('[LibraryStorage] Failed to store manual:', request.error);
                reject(request.error);
            };
        });
    }

    /**
     * Store manual item data
     */
    async storeManualItem(item) {
        if (!this.db) return false;

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['manualItems'], 'readwrite');
            const store = transaction.objectStore('manualItems');
            
            const itemData = {
                id: item.id || item.iid,
                manual_uid: item.manual_uid,
                name: item.name,
                file_path: item.file_path,
                cachedAt: new Date().toISOString(),
                ...item
            };

            const request = store.put(itemData);

            request.onsuccess = () => {
                console.log('[LibraryStorage] Manual item stored successfully:', item.name);
                resolve(true);
            };

            request.onerror = () => {
                console.error('[LibraryStorage] Failed to store manual item:', request.error);
                reject(request.error);
            };
        });
    }

    /**
     * Store manual content data
     */
    async storeManualContent(content) {
        if (!this.db) return false;

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['manualContent'], 'readwrite');
            const store = transaction.objectStore('manualContent');
            
            const contentData = {
                id: content.id || content.cid,
                item_uid: content.item_uid,
                name: content.name,
                file_path: content.file_path,
                content_type: content.content_type,
                encrypted_data: content.encrypted_data, // Store encrypted data
                cachedAt: new Date().toISOString(),
                ...content
            };

            const request = store.put(contentData);

            request.onsuccess = () => {
                console.log('[LibraryStorage] Manual content stored successfully:', content.name);
                resolve(true);
            };

            request.onerror = () => {
                console.error('[LibraryStorage] Failed to store manual content:', request.error);
                reject(request.error);
            };
        });
    }

    /**
     * Store encrypted manual data
     */
    async storeEncryptedManual(manualData) {
        if (!this.db) return false;

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['manualContent'], 'readwrite');
            const store = transaction.objectStore('manualContent');
            
            const encryptedManualData = {
                id: manualData.id,
                item_uid: manualData.item_uid || manualData.id,
                name: manualData.name,
                encrypted_data: manualData.encrypted_data,
                content_type: 'application/pdf',
                is_encrypted: true,
                encryption_timestamp: Date.now(),
                cachedAt: new Date().toISOString()
            };

            const request = store.put(encryptedManualData);

            request.onsuccess = () => {
                console.log('[LibraryStorage] Encrypted manual stored successfully:', manualData.name);
                resolve(true);
            };

            request.onerror = () => {
                console.error('[LibraryStorage] Failed to store encrypted manual:', request.error);
                reject(request.error);
            };
        });
    }

    /**
     * Get encrypted manual data by ID
     */
    async getEncryptedManual(manualId) {
        if (!this.db) return null;

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['manualContent'], 'readonly');
            const store = transaction.objectStore('manualContent');
            const request = store.get(manualId);

            request.onsuccess = () => {
                const result = request.result;
                if (result && result.is_encrypted) {
                    resolve(result);
                } else {
                    resolve(null);
                }
            };

            request.onerror = () => {
                console.error('[LibraryStorage] Failed to get encrypted manual:', request.error);
                reject(request.error);
            };
        });
    }

    /**
     * Clear expired encrypted data
     */
    async clearExpiredEncryption(maxAge = 24 * 60 * 60 * 1000) { // 24 hours default
        if (!this.db) return false;

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['manualContent'], 'readwrite');
            const store = transaction.objectStore('manualContent');
            const request = store.getAll();

            request.onsuccess = () => {
                const allData = request.result;
                const currentTime = Date.now();
                let deletedCount = 0;

                const deletePromises = allData
                    .filter(item => {
                        return item.is_encrypted && 
                               item.encryption_timestamp && 
                               (currentTime - item.encryption_timestamp) > maxAge;
                    })
                    .map(item => {
                        return new Promise((deleteResolve, deleteReject) => {
                            const deleteRequest = store.delete(item.id);
                            deleteRequest.onsuccess = () => {
                                deletedCount++;
                                deleteResolve();
                            };
                            deleteRequest.onerror = () => deleteReject(deleteRequest.error);
                        });
                    });

                Promise.all(deletePromises)
                    .then(() => {
                        console.log(`[LibraryStorage] Cleared ${deletedCount} expired encrypted items`);
                        resolve(deletedCount);
                    })
                    .catch(reject);
            };

            request.onerror = () => {
                console.error('[LibraryStorage] Failed to clear expired encryption:', request.error);
                reject(request.error);
            };
        });
    }

    /**
     * Get all manuals
     */
    async getAllManuals() {
        if (!this.db) return [];

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['manuals'], 'readonly');
            const store = transaction.objectStore('manuals');
            const request = store.getAll();

            request.onsuccess = () => {
                resolve(request.result || []);
            };

            request.onerror = () => {
                console.error('[LibraryStorage] Failed to get manuals:', request.error);
                reject(request.error);
            };
        });
    }

    /**
     * Get manual by ID
     */
    async getManual(id) {
        if (!this.db) return null;

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['manuals'], 'readonly');
            const store = transaction.objectStore('manuals');
            const request = store.get(id);

            request.onsuccess = () => {
                resolve(request.result || null);
            };

            request.onerror = () => {
                console.error('[LibraryStorage] Failed to get manual:', request.error);
                reject(request.error);
            };
        });
    }

    /**
     * Get manual items by manual ID
     */
    async getManualItems(manualId) {
        if (!this.db) return [];

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['manualItems'], 'readonly');
            const store = transaction.objectStore('manualItems');
            const index = store.index('manual_uid');
            const request = index.getAll(manualId);

            request.onsuccess = () => {
                resolve(request.result || []);
            };

            request.onerror = () => {
                console.error('[LibraryStorage] Failed to get manual items:', request.error);
                reject(request.error);
            };
        });
    }

    /**
     * Get manual content by item ID
     */
    async getManualContent(itemId) {
        if (!this.db) return [];

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['manualContent'], 'readonly');
            const store = transaction.objectStore('manualContent');
            const index = store.index('item_uid');
            const request = index.getAll(itemId);

            request.onsuccess = () => {
                resolve(request.result || []);
            };

            request.onerror = () => {
                console.error('[LibraryStorage] Failed to get manual content:', request.error);
                reject(request.error);
            };
        });
    }

    /**
     * Search manuals by name
     */
    async searchManuals(query) {
        const manuals = await this.getAllManuals();
        const searchTerm = query.toLowerCase();
        
        return manuals.filter(manual => 
            manual.name.toLowerCase().includes(searchTerm)
        );
    }

    /**
     * Store cache metadata
     */
    async storeCacheMetadata(url, type, size = 0) {
        if (!this.db) return false;

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['cacheMetadata'], 'readwrite');
            const store = transaction.objectStore('cacheMetadata');
            
            const metadata = {
                url,
                type,
                size,
                timestamp: new Date().toISOString()
            };

            const request = store.put(metadata);

            request.onsuccess = () => resolve(true);
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Get cache statistics
     */
    async getCacheStats() {
        console.log('[LibraryStorage] Getting cache stats...');
        
        if (!this.db) {
            console.warn('[LibraryStorage] Database not initialized, attempting to initialize...');
            try {
                await this.init();
                if (!this.db) {
                    console.error('[LibraryStorage] Failed to initialize database');
                    return null;
                }
            } catch (error) {
                console.error('[LibraryStorage] Database initialization failed:', error);
                return null;
            }
        }

        try {
            const manualsCount = await this.getCount('manuals');
            const itemsCount = await this.getCount('manualItems');
            const contentCount = await this.getCount('manualContent');
            const cacheMetadata = await this.getCount('cacheMetadata');
            const lastSync = await this.getUserPreference('lastSync');

            console.log('[LibraryStorage] Cache stats:', {
                manualsCount,
                itemsCount,
                contentCount,
                cacheMetadata,
                lastSync
            });

            return {
                manualsCount,
                itemsCount,
                contentCount,
                cacheMetadata,
                lastSync
            };
        } catch (error) {
            console.error('[LibraryStorage] Error getting cache stats:', error);
            return null;
        }
    }

    /**
     * Get count of records in a store
     */
    async getCount(storeName) {
        if (!this.db) return 0;

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([storeName], 'readonly');
            const store = transaction.objectStore(storeName);
            const request = store.count();

            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Store user preference
     */
    async setUserPreference(key, value) {
        if (!this.db) return false;

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['userPreferences'], 'readwrite');
            const store = transaction.objectStore('userPreferences');
            const request = store.put({ key, value, updatedAt: new Date().toISOString() });

            request.onsuccess = () => resolve(true);
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Get user preference
     */
    async getUserPreference(key) {
        if (!this.db) return null;

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['userPreferences'], 'readonly');
            const store = transaction.objectStore('userPreferences');
            const request = store.get(key);

            request.onsuccess = () => {
                const result = request.result;
                resolve(result ? result.value : null);
            };

            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Clear all data
     */
    async clearAllData() {
        if (!this.db) return false;

        const storeNames = ['manuals', 'manualItems', 'manualContent', 'cacheMetadata'];
        
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(storeNames, 'readwrite');
            
            let completed = 0;
            const total = storeNames.length;

            const checkCompletion = () => {
                completed++;
                if (completed === total) {
                    console.log('[LibraryStorage] All data cleared successfully');
                    resolve(true);
                }
            };

            storeNames.forEach(storeName => {
                const store = transaction.objectStore(storeName);
                const request = store.clear();
                
                request.onsuccess = checkCompletion;
                request.onerror = () => {
                    console.error(`[LibraryStorage] Failed to clear ${storeName}:`, request.error);
                    reject(request.error);
                };
            });
        });
    }

    /**
     * Check if we're currently offline
     */
    isOffline() {
        return !navigator.onLine;
    }

    /**
     * Sync data when coming back online
     */
    async syncOnline() {
        if (this.isOffline()) {
            console.log('[LibraryStorage] Still offline, skipping sync');
            return false;
        }

        try {
            console.log('[LibraryStorage] Starting online sync...');
            
            // Update last sync timestamp
            await this.setUserPreference('lastSync', new Date().toISOString());
            
            // Here you can add logic to fetch latest data from server
            // and update the local storage
            
            console.log('[LibraryStorage] Online sync completed');
            return true;
        } catch (error) {
            console.error('[LibraryStorage] Sync failed:', error);
            return false;
        }
    }
}

// Global instance
window.libraryStorage = new LibraryStorageManager();

// Global flag to track initialization status
window.libraryStorageReady = false;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', async () => {
    try {
        await window.libraryStorage.init();
        window.libraryStorageReady = true;
        console.log('[LibraryStorage] Initialized successfully');
        
        // Dispatch custom event to notify other scripts
        window.dispatchEvent(new CustomEvent('libraryStorageReady', {
            detail: { storage: window.libraryStorage }
        }));
        
        // Auto-sync when coming back online
        window.addEventListener('online', () => {
            setTimeout(() => {
                window.libraryStorage.syncOnline();
            }, 1000);
        });
        
    } catch (error) {
        console.error('[LibraryStorage] Initialization failed:', error);
    }
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = LibraryStorageManager;
}
