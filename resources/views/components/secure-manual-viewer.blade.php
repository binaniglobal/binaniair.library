{{-- PDF Manual Viewer Component --}}
<div id="manual-viewer-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="manualViewerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manualViewerModalLabel">
                    <i class="mdi mdi-file-pdf-box me-2"></i>
                    <span id="manual-viewer-title">Manual Viewer</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe
                    id="manual-viewer-iframe"
                    style="width: 100%; height: 100%; border: none;"
                    allow="fullscreen"
                    loading="lazy">
                </iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="mdi mdi-close"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * PDF Manual Viewer Controller
 */
class ManualViewer {
    constructor() {
        this.modalElement = document.getElementById('manual-viewer-modal');
        this.modal = new bootstrap.Modal(this.modalElement);
        this.iframe = document.getElementById('manual-viewer-iframe');
        this.titleElement = document.getElementById('manual-viewer-title');

        this.setupEventListeners();
    }

    setupEventListeners() {
        // Clean up on modal close
        this.modalElement.addEventListener('hidden.bs.modal', () => {
            this.cleanup();
        });
    }

    /**
     * Load and display a manual PDF.
     * @param {string|number} manualId - The ID of the manual.
     * @param {string} manualName - The name of the manual for the title.
     */
    loadManual(manualId, manualName) {
        // Construct the URL to the PDF.
        // This assumes a route exists like '/manuals/{id}/view' that serves the PDF.
        const manualUrl = `/manuals/${manualId}/view`;

        this.titleElement.textContent = manualName || 'Manual Viewer';
        this.iframe.src = manualUrl;
        this.modal.show();
    }

    /**
     * Clean up resources
     */
    cleanup() {
        // Clear the iframe src to stop loading and free up memory
        this.iframe.src = 'about:blank';
        this.titleElement.textContent = 'Manual Viewer';
    }
}

// Initialize viewer when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.manualViewer = new ManualViewer();
});

// Global function to open the viewer, maintaining the original function name for compatibility
window.openSecureViewer = function(manualId, manualName) {
    if (window.manualViewer) {
        window.manualViewer.loadManual(manualId, manualName);
    } else {
        console.error('[ManualViewer] Viewer not initialized');
    }
};
</script>
