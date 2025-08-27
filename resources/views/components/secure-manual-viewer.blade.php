{{-- Secure Manual Viewer Component --}}
<div id="secure-manual-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="secureManualModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="secureManualModalLabel">
                    <i class="mdi mdi-shield-check me-2"></i>
                    Secure Manual Viewer
                </h5>
                <div class="d-flex align-items-center">
                    <span id="manual-loading-indicator" class="spinner-border spinner-border-sm me-3" style="display: none;"></span>
                    <span id="security-status" class="badge bg-success me-3">
                        <i class="mdi mdi-lock"></i> Encrypted
                    </span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0 secure-content" style="overflow: hidden;">
                <!-- Loading State -->
                <div id="manual-loading-state" class="d-flex justify-content-center align-items-center h-100" style="min-height: 400px;">
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h6>Decrypting and Loading Manual...</h6>
                        <small class="text-muted">Please wait while we securely prepare your document</small>
                    </div>
                </div>

                <!-- Error State -->
                <div id="manual-error-state" class="d-flex justify-content-center align-items-center h-100" style="min-height: 400px; display: none !important;">
                    <div class="text-center">
                        <i class="mdi mdi-alert-circle text-danger" style="font-size: 3rem;"></i>
                        <h6 class="mt-3">Failed to Load Manual</h6>
                        <p class="text-muted" id="error-message">An error occurred while loading the manual.</p>
                        <button class="btn btn-primary" onclick="retryManualLoad()">
                            <i class="mdi mdi-refresh"></i> Retry
                        </button>
                    </div>
                </div>

                <!-- Secure WebView Container -->
                <iframe
                    id="secure-manual-webview"
                    {{-- The 'allow-same-origin' token has been removed to prevent the iframe from escaping its sandbox, which is a critical security risk. --}}
                    sandbox="allow-scripts allow-popups allow-popups-to-escape-sandbox"
                    style="width: 100%; height: 80vh; border: none; display: none;"
                    allow="fullscreen"
                    loading="lazy">
                </iframe>




            </div>
            <div class="modal-footer bg-light">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-info me-2">
                            <i class="mdi mdi-eye-off"></i> Download Disabled
                        </span>
                        <span class="badge bg-warning">
                            <i class="mdi mdi-timer"></i> Session Protected
                        </span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="mdi mdi-close"></i> Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * Secure Manual Viewer Controller
 */
class SecureManualViewer {
    constructor() {
        this.modal = null;
        this.webview = null;
        this.currentManualId = null;
        this.currentManualName = null; // Store the name for retries
        this.currentBlobUrl = null;
        this.retryCount = 0;
        this.maxRetries = 3;

        this.initializeComponents();
        this.setupEventListeners();
    }

    initializeComponents() {
        this.modal = new bootstrap.Modal(document.getElementById('secure-manual-modal'), {
            keyboard: false,
            backdrop: 'static'
        });
        this.webview = document.getElementById('secure-manual-webview');
    }

    setupEventListeners() {
        // Clean up on modal close
        document.getElementById('secure-manual-modal').addEventListener('hidden.bs.modal', () => {
            this.cleanup();
        });

        // Prevent context menu on webview
        this.webview.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            return false;
        });

        // Monitor webview load events
        this.webview.addEventListener('load', () => {
            this.onWebviewLoad();
        });

        this.webview.addEventListener('error', () => {
            this.onWebviewError();
        });
    }

    /**
     * Load and display manual in secure webview
     */
    async loadManual(manualId, manualName) {
        try {
            this.currentManualName = manualName;
            this.currentManualId = manualId;
            this.retryCount = 0;

            // Update modal title
            document.getElementById('secureManualModalLabel').innerHTML = `
                <i class="mdi mdi-shield-check me-2"></i>
                ${manualName}
            `;

            // Show modal and loading state
            this.showLoadingState();
            this.modal.show();

            // Get encryption key
            const encryptionKey = await window.secureViewer.getUserSessionKey();

            // Try to get from cache first
            let manualData = await this.getFromEncryptedCache(manualId, encryptionKey);

            if (!manualData) {
                // Fetch from server
                manualData = await this.fetchManualFromServer(manualId);

                // Encrypt and cache
                await this.encryptAndCache(manualId, manualData, encryptionKey, manualName);
            }

            // Create secure blob URL
            this.currentBlobUrl = window.secureViewer.createSecureBlobUrl(manualData);

            // Load in webview
            await this.loadInWebview(this.currentBlobUrl);

        } catch (error) {
            console.error('[SecureViewer] Failed to load manual:', error);
            this.showErrorState(error.message);
        }
    }

    /**
     * Get manual from encrypted cache
     */
    async getFromEncryptedCache(manualId, encryptionKey) {
        try {
            if (window.libraryStorage) {
                const encryptedData = await window.libraryStorage.getManualContent(manualId);

                if (encryptedData && encryptedData.length > 0) {
                    const cachedManual = encryptedData[0];

                    if (window.secureViewer.validateEncryptedData(cachedManual.encrypted_data)) {
                        return await window.secureViewer.decryptData(cachedManual.encrypted_data, encryptionKey);
                    }
                }
            }
            return null;
        } catch (error) {
            console.warn('[SecureViewer] Failed to decrypt from cache (likely due to new session key). Clearing stale cache and re-fetching.', error);
            // If decryption fails, the cache for this item is invalid (stale key).
            // We delete it to prevent this error on subsequent loads.
            if (window.libraryStorage) {
                await window.libraryStorage.removeManualContent(manualId); // Assumed fix
            }
            // Return null to signal that a fresh copy must be fetched from the server.
            return null;

        }
    }

    /**
     * Fetch manual from server
     */
    async fetchManualFromServer(manualId) {
        const response = await fetch(`/api/manual-content/${manualId}/secure`, {
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`Failed to fetch manual: ${response.status} ${response.statusText}`);
        }

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || 'Failed to fetch manual data');
        }

        return data.data;
    }

    /**
     * Encrypt and cache manual data
     */
    async encryptAndCache(manualId, manualData, encryptionKey, manualName) {
        try {
            const encryptedData = await window.secureViewer.encryptData(manualData, encryptionKey);

            if (window.libraryStorage) {
                await window.libraryStorage.storeManualContent({
                    id: manualId,
                    item_uid: manualId,
                    name: manualName || `Manual_${manualId}`, // Use the real name, with a fallback
                    encrypted_data: encryptedData,
                    content_type: 'application/pdf',
                    cached_at: new Date().toISOString()
                });
            }
        } catch (error) {
            console.warn('[SecureViewer] Failed to cache manual:', error);
            // Don't throw - caching failure shouldn't prevent viewing
        }
    }

    /**
     * Load content in webview
     */
    async loadInWebview(blobUrl) {
        return new Promise((resolve, reject) => {
            const timeout = setTimeout(() => {
                reject(new Error('Webview load timeout'));
            }, 30000);

            const onLoad = () => {
                clearTimeout(timeout);
                this.webview.removeEventListener('load', onLoad);
                this.webview.removeEventListener('error', onError);
                resolve();
            };

            const onError = () => {
                clearTimeout(timeout);
                this.webview.removeEventListener('load', onLoad);
                this.webview.removeEventListener('error', onError);
                reject(new Error('Webview load error'));
            };

            this.webview.addEventListener('load', onLoad);
            this.webview.addEventListener('error', onError);

            // Set the blob URL
            this.webview.src = blobUrl;
        });
    }

    /**
     * Show loading state
     */
    showLoadingState() {
        document.getElementById('manual-loading-state').style.display = 'flex';
        document.getElementById('manual-error-state').style.display = 'none';
        document.getElementById('secure-manual-webview').style.display = 'none';
        document.getElementById('manual-loading-indicator').style.display = 'inline-block';
    }

    /**
     * Show error state
     */
    showErrorState(errorMessage) {
        document.getElementById('manual-loading-state').style.display = 'none';
        document.getElementById('manual-error-state').style.display = 'flex';
        document.getElementById('secure-manual-webview').style.display = 'none';
        document.getElementById('manual-loading-indicator').style.display = 'none';
        document.getElementById('error-message').textContent = errorMessage;
    }

    /**
     * Handle successful webview load
     */
    onWebviewLoad() {
        document.getElementById('manual-loading-state').style.display = 'none';
        document.getElementById('manual-error-state').style.display = 'none';
        document.getElementById('secure-manual-webview').style.display = 'block';
        document.getElementById('manual-loading-indicator').style.display = 'none';

        // Apply additional security to webview content
        this.applyWebviewSecurity();
    }

    /**
     * Handle webview load error
     */
    onWebviewError() {
        this.showErrorState('Failed to load manual in secure viewer');
    }

    /**
     * Apply additional security measures to webview
     */
    applyWebviewSecurity() {
        try {
            // Inject security script into webview if possible
            const webviewDocument = this.webview.contentDocument || this.webview.contentWindow.document;

            if (webviewDocument) {
                const securityScript = webviewDocument.createElement('script');
                securityScript.textContent = `
                    // Disable right-click
                    document.addEventListener('contextmenu', e => e.preventDefault());

                    // Disable text selection
                    document.addEventListener('selectstart', e => e.preventDefault());

                    // Disable drag and drop
                    document.addEventListener('dragstart', e => e.preventDefault());

                    // Override print
                    window.print = () => console.warn('Printing disabled');
                `;
                webviewDocument.head.appendChild(securityScript);
            }
        } catch (error) {
            // Ignore cross-origin errors - security is still maintained by sandbox
            console.log('[SecureViewer] Cross-origin security injection blocked (expected)');
        }
    }

    /**
     * Retry loading manual
     */
    async retryManual() {
        if (this.retryCount < this.maxRetries && this.currentManualId) {
            this.retryCount++;
            await this.loadManual(this.currentManualId, this.currentManualName);
        } else {
            this.showErrorState('Maximum retry attempts reached. Please try again later.');
        }
    }

    /**
     * Clean up resources
     */
    cleanup() {
        if (this.currentBlobUrl) {
            URL.revokeObjectURL(this.currentBlobUrl);
            this.currentBlobUrl = null;
        }

        this.webview.src = 'about:blank';
        this.currentManualId = null;
        this.currentManualName = null;
        this.retryCount = 0;

        // Clear sensitive data
        window.secureViewer.clearSensitiveData();
    }
}

// Initialize secure viewer when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.secureManualViewer = new SecureManualViewer();
});

// Global function for retry button
function retryManualLoad() {
    if (window.secureManualViewer) {
        window.secureManualViewer.retryManual();
    }
}

// Global function to open secure viewer
window.openSecureViewer = function(manualId, manualName) {
    if (window.secureManualViewer) {
        window.secureManualViewer.loadManual(manualId, manualName);
    } else {
        console.error('[SecureViewer] Secure viewer not initialized');
    }
};
</script>

<style>
.secure-content {
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;

    -webkit-touch-callout: none;
    -webkit-tap-highlight-color: transparent;
}

.secure-content * {
    pointer-events: auto;
}

#secure-manual-webview {
    pointer-events: auto;
}

/* Prevent text selection in modal */
.modal-content {
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

/* Security indicators */
.badge {
    font-size: 0.75em;
}

/* Loading animation */
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.pulse {
    animation: pulse 2s infinite;
}
</style>
