/**
 * Security Fixes and Error Handling for Secure Viewer
 */

// Wait for all dependencies to load before initializing
document.addEventListener('DOMContentLoaded', function() {
    // Ensure CryptoJS is loaded
    if (typeof CryptoJS === 'undefined') {
        console.error('[SecurityFix] CryptoJS not loaded. Secure viewer will not work.');
        return;
    }

    // Ensure secure viewer dependencies are available
    if (!window.secureViewer || !window.libraryStorage) {
        console.warn('[SecurityFix] Dependencies not fully loaded. Retrying in 1 second...');
        setTimeout(() => {
            if (!window.secureViewer || !window.libraryStorage) {
                console.error('[SecurityFix] Critical dependencies missing. Secure viewer may not function properly.');
            }
        }, 1000);
    }

    // Add global error handler for secure viewer
    window.addEventListener('error', function(event) {
        if (event.error && event.error.message && event.error.message.includes('SecureViewer')) {
            console.error('[SecurityFix] Secure viewer error caught:', event.error);
            
            // Show user-friendly error
            if (typeof showNotification === 'function') {
                showNotification('error', 'Secure viewer encountered an error. Please refresh and try again.');
            }
        }
    });

    // Override console.error to catch CryptoJS errors
    const originalConsoleError = console.error;
    console.error = function(...args) {
        const message = args.join(' ');
        if (message.includes('CryptoJS') || message.includes('crypto-js')) {
            // Show user-friendly message for crypto errors
            if (typeof showNotification === 'function') {
                showNotification('error', 'Encryption library error. Please refresh the page.');
            }
        }
        originalConsoleError.apply(console, args);
    };

    console.log('[SecurityFix] Security fixes initialized');
});

/**
 * Enhanced error handling for fetch operations
 */
window.secureApiCall = async function(url, options = {}) {
    try {
        const defaultOptions = {
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        };

        const response = await fetch(url, { ...defaultOptions, ...options });
        
        if (!response.ok) {
            if (response.status === 401) {
                throw new Error('Authentication required. Please log in again.');
            } else if (response.status === 403) {
                throw new Error('Access denied. You do not have permission to view this content.');
            } else if (response.status === 404) {
                throw new Error('Content not found.');
            } else {
                throw new Error(`Server error: ${response.status} ${response.statusText}`);
            }
        }

        return response;
    } catch (error) {
        console.error('[SecurityFix] API call failed:', error);
        throw error;
    }
};

/**
 * Secure cleanup function
 */
window.secureCleanup = function() {
    // Clear any sensitive data from memory
    if (window.secureViewer && typeof window.secureViewer.clearSensitiveData === 'function') {
        window.secureViewer.clearSensitiveData();
    }

    // Clear any cached blob URLs
    if (window.secureManualViewer && window.secureManualViewer.currentBlobUrl) {
        URL.revokeObjectURL(window.secureManualViewer.currentBlobUrl);
        window.secureManualViewer.currentBlobUrl = null;
    }

    console.log('[SecurityFix] Secure cleanup completed');
};

// Clean up on page unload
window.addEventListener('beforeunload', window.secureCleanup);
window.addEventListener('unload', window.secureCleanup);
