/**
 * Secure Manual Viewer - Encryption Layer
 * Handles AES encryption/decryption for manual data storage
 */

class SecureViewer {
    constructor() {
        this.algorithm = 'AES';
        this.keyLength = 256;
        this.ivLength = 16;
    }

    /**
     * Derive encryption key from user session data
     */
    deriveKey(sessionToken, userId) {
        const combined = `${sessionToken}_${userId}_${new Date().toDateString()}`;
        return CryptoJS.SHA256(combined).toString(CryptoJS.enc.Hex);
    }

    /**
     * Encrypt data using AES-256
     */
    async encryptData(data, key) {
        try {
            const encrypted = CryptoJS.AES.encrypt(JSON.stringify(data), key, {
                mode: CryptoJS.mode.CBC,
                padding: CryptoJS.pad.Pkcs7
            });
            
            return {
                encrypted: encrypted.toString(),
                timestamp: Date.now(),
                checksum: CryptoJS.SHA256(JSON.stringify(data)).toString()
            };
        } catch (error) {
            console.error('[SecureViewer] Encryption failed:', error);
            throw new Error('Failed to encrypt data');
        }
    }

    /**
     * Decrypt data using AES-256
     */
    async decryptData(encryptedData, key) {
        try {
            if (!encryptedData.encrypted || !encryptedData.checksum) {
                throw new Error('Invalid encrypted data format');
            }

            const decrypted = CryptoJS.AES.decrypt(encryptedData.encrypted, key, {
                mode: CryptoJS.mode.CBC,
                padding: CryptoJS.pad.Pkcs7
            });
            
            const decryptedString = decrypted.toString(CryptoJS.enc.Utf8);
            if (!decryptedString) {
                throw new Error('Failed to decrypt data - invalid key or corrupted data');
            }

            const data = JSON.parse(decryptedString);
            
            // Verify data integrity
            const currentChecksum = CryptoJS.SHA256(JSON.stringify(data)).toString();
            if (currentChecksum !== encryptedData.checksum) {
                throw new Error('Data integrity check failed - possible tampering detected');
            }

            return data;
        } catch (error) {
            console.error('[SecureViewer] Decryption failed:', error);
            throw new Error('Failed to decrypt data');
        }
    }

    /**
     * Generate secure blob URL for webview consumption
     */
    createSecureBlobUrl(decryptedData, mimeType = 'application/pdf') {
        try {
            // Convert base64 to binary if needed
            let binaryData;
            if (typeof decryptedData === 'string' && decryptedData.startsWith('data:')) {
                // Handle data URL
                const base64Data = decryptedData.split(',')[1];
                binaryData = atob(base64Data);
            } else if (typeof decryptedData === 'string') {
                // Handle base64 string
                binaryData = atob(decryptedData);
            } else {
                // Handle binary data
                binaryData = decryptedData;
            }

            // Convert to Uint8Array
            const bytes = new Uint8Array(binaryData.length);
            for (let i = 0; i < binaryData.length; i++) {
                bytes[i] = binaryData.charCodeAt(i);
            }

            // Create blob with proper MIME type
            const blob = new Blob([bytes], { type: mimeType });
            
            // Create secure URL with expiration
            const blobUrl = URL.createObjectURL(blob);
            
            // Auto-revoke URL after 5 minutes for security
            setTimeout(() => {
                URL.revokeObjectURL(blobUrl);
                console.log('[SecureViewer] Blob URL revoked for security');
            }, 5 * 60 * 1000);

            return blobUrl;
        } catch (error) {
            console.error('[SecureViewer] Failed to create blob URL:', error);
            throw new Error('Failed to create secure blob URL');
        }
    }

    /**
     * Get user session key for encryption
     */
    async getUserSessionKey() {
        try {
            const response = await fetch('/pwa/auth-token', {
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const tokenData = await response.json();
                return this.deriveKey(tokenData.token, tokenData.user.id);
            } else {
                throw new Error('Failed to get session token');
            }
        } catch (error) {
            console.error('[SecureViewer] Failed to get session key:', error);
            // Fallback to session-based key
            return this.deriveKey(document.querySelector('meta[name="csrf-token"]').content, 'fallback');
        }
    }

    /**
     * Validate encrypted data integrity
     */
    validateEncryptedData(encryptedData) {
        return encryptedData && 
               typeof encryptedData === 'object' &&
               encryptedData.encrypted &&
               encryptedData.checksum &&
               encryptedData.timestamp;
    }

    /**
     * Clear sensitive data from memory
     */
    clearSensitiveData() {
        // Force garbage collection if available
        if (window.gc) {
            window.gc();
        }
    }
}

// Create global instance
window.secureViewer = new SecureViewer();

// Download prevention functions
window.preventDownloads = function() {
    // Prevent right-click context menu
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        return false;
    });

    // Prevent common keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Prevent Ctrl+S (Save)
        if (e.ctrlKey && (e.key === 's' || e.key === 'S')) {
            e.preventDefault();
            return false;
        }
        
        // Prevent Ctrl+Shift+S (Save As)
        if (e.ctrlKey && e.shiftKey && (e.key === 's' || e.key === 'S')) {
            e.preventDefault();
            return false;
        }
        
        // Prevent F12 (Developer Tools)
        if (e.key === 'F12') {
            e.preventDefault();
            return false;
        }
        
        // Prevent Ctrl+Shift+I (Developer Tools)
        if (e.ctrlKey && e.shiftKey && (e.key === 'i' || e.key === 'I')) {
            e.preventDefault();
            return false;
        }
        
        // Prevent Ctrl+U (View Source)
        if (e.ctrlKey && (e.key === 'u' || e.key === 'U')) {
            e.preventDefault();
            return false;
        }
    });

    // Prevent drag and drop
    document.addEventListener('dragstart', function(e) {
        e.preventDefault();
        return false;
    });

    // Prevent selection in sensitive areas
    document.addEventListener('selectstart', function(e) {
        if (e.target.closest('.secure-content')) {
            e.preventDefault();
            return false;
        }
    });

    // Override print function
    window.print = function() {
        console.warn('[SecureViewer] Printing is disabled for security');
        return false;
    };
};

// Initialize download prevention on load
document.addEventListener('DOMContentLoaded', function() {
    window.preventDownloads();
    console.log('[SecureViewer] Security measures initialized');
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SecureViewer;
}
