@php use App\Models\ManualItemContent;use App\Models\Manuals;use Illuminate\Support\Facades\Auth; @endphp
@extends('layouts.app')

@section('content')
    @push('styles')
        <!-- Vendors CSS -->
        <link rel="stylesheet" href="{{ url('storage/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}"/>
        <link rel="stylesheet" href="{{ url('storage/assets/vendor/libs/typeahead-js/typeahead.css') }}"/>
        <link rel="stylesheet" href="{{ url('storage/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}"/>
        <link rel="stylesheet"
              href="{{ url('storage/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}"/>
        <link rel="stylesheet" href="{{ url('storage/assets/vendor/libs/flatpickr/flatpickr.css') }}"/>
    @endpush
    <div class="container-xxl flex-grow-1 container-p-y">
        @php
            $user = Auth::user();
            $size = new \App\Http\Controllers\ManualsItemController();
        @endphp
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home / <a href="{{ route('manual.index') }}">Manuals</a> / <a
                    href="{{ route('manual.items.index', ['id' => $Manual->manual_uid]) }}">{{ (getParentManual($Manual->manual_uid))->name }}</a></span>
            / {{ $Manual->name }}</h4>

        <!-- Responsive Datatable -->
        <div class="card">
            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            <h5 class="card-header">{{ ucfirst($Manual->name) ? ucfirst($Manual->name): ''}}</h5>
            <div class="card-datatable table-responsive text-nowrap">
                <table class="dt-responsive table table-hover" id="dt-responsive">
                    <thead>
                    <tr>
                        <th>{{ $Manual->name }}</th>
                        <th>File Type</th>
                        <th>Size</th>
                        @can('destroy-manual')
                            <th>Action</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @can('view-manual')
                        @foreach($Items as $items)
                            @php
                                $parentManual = Manuals::where('mid', $items->manual_uid)->first();
                            @endphp
                            @can('access-manual-'.$parentManual->name.'.'. $Manual->name.'.'.$items->name)
                                <tr>
                                    <td>
                                        <div class="btn-group">
                                            @if($items->file_type === 'application/pdf')
                                                <a class="btn btn-link p-0 text-start pdf-modal-trigger"
                                                   href="{{ route('manual.items.content.view', ['filename' => $items->link]) }}"
                                                   data-doc-name="{{ $items->name }}">
                                                    <i class="mdi mdi-file-pdf-box text-danger me-1"></i>
                                                    {{ $items->name }}
                                                </a>
                                                <span class="cache-status text-success ms-2" data-raw-url="{{ route('manual.items.content.raw', ['filename' => $items->link]) }}"></span>
                                            @else
                                                <span class="text-muted">
                                                    <i class="mdi mdi-file me-1"></i>
                                                    {{ $items->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $items->file_type=='application/pdf'?'PDF':$items->file_type }}</td>
                                    <td>{{ $size->formatBytes($items->file_size) }}</td>
                                    @can('destroy-manual')
                                        <td>
                                            <div class="dropdown" data-cache-container data-raw-url="{{ route('manual.items.content.raw', ['filename' => $items->link]) }}">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                        data-bs-toggle="dropdown">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    @if($items->file_type === 'application/pdf')
                                                        <a class="dropdown-item cache-doc-btn"
                                                           href="#"
                                                           data-doc-name="{{ $items->name }}"
                                                           data-pwa-url="{{ getPwaSubManualContentUrl($items->link) }}">
                                                            <i class="mdi mdi-download me-1"></i> Save Offline
                                                        </a>
                                                        <a class="dropdown-item update-cache-btn"
                                                           href="#"
                                                           style="display: none;"
                                                           data-doc-name="{{ $items->name }}"
                                                           data-pwa-url="{{ getPwaSubManualContentUrl($items->link) }}">
                                                            <i class="mdi mdi-refresh me-1"></i> Update Offline
                                                        </a>
                                                    @endif
                                                    <a class="dropdown-item"
                                                       href="{{ route('manual.items.content.destroy', ['id'=>$items->manual_uid, 'ids'=>$items->micd]) }}"
                                                    ><i class="mdi mdi-trash-can-outline me-1"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    @endcan
                                </tr>
                            @endcan
                        @endforeach
                    @endcan
                    </tbody>
                </table>
            </div>
        </div>
        <!--/ Responsive Datatable -->
    </div>

    <!-- PDF Viewer Modal -->
    <div class="modal fade" id="pdfViewerModal" tabindex="-1" aria-labelledby="pdfViewerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfViewerModalLabel">PDF Viewer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="pdf-iframe" src="" frameborder="0" width="100%" style="height: 80vh;"></iframe>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                updateServiceWorkerAuthToken();
                updateAllCacheStatus();

                $("#dt-responsive").DataTable({
                    "responsive": true, "lengthChange": false, "autoWidth": false, ordering: false,
                    buttons: [
                        'pageLength',
                            @can('create-manual')
                        {
                            html: '<a class="btn btn-primary" href="{{ route('manual.items.content.show', $Id) }}"><span class="fa fa-plus-circle" aria-hidden="true"></span> ' +
                                ' <i class="menu-icon tf-icons mdi mdi-file-document"></i>' +
                                ' </a>',
                            attr: {
                                class: 'btn btn-primary'
                            },
                        }
                        @endcan
                    ],
                    "language": {
                        "emptyTable": "<span class='text-success'>Empty table</span>"
                    }
                }).buttons().container().appendTo('#dt-responsive_wrapper .col-md-6:eq(0)');

                // Handle PDF link clicks
                $('.pdf-modal-trigger').on('click', function(e) {
                    e.preventDefault();
                    const pdfUrl = $(this).attr('href');
                    const docName = $(this).data('doc-name');
                    $('#pdf-iframe').attr('src', pdfUrl);
                    $('#pdfViewerModalLabel').text(docName);
                    $('#pdfViewerModal').modal('show');
                });

                // Clear iframe src when modal is hidden to stop loading
                $('#pdfViewerModal').on('hidden.bs.modal', function () {
                    $('#pdf-iframe').attr('src', '');
                    $('#pdfViewerModalLabel').text('PDF Viewer'); // Reset title
                });

                // Caching functionality
                $(document).on('click', '.cache-doc-btn, .update-cache-btn', async function(e) {
                    e.preventDefault();

                    const $btn = $(this);
                    const isUpdate = $btn.hasClass('update-cache-btn');
                    const docName = $btn.data('doc-name');
                    const rawUrl = $btn.closest('[data-cache-container]').data('raw-url');
                    const pwaUrl = $btn.data('pwa-url');

                    const originalHtml = $btn.html();
                    $btn.html('<i class="mdi mdi-loading mdi-spin"></i> ' + (isUpdate ? 'Updating...' : 'Saving...'));
                    $btn.prop('disabled', true);

                    try {
                        await updateServiceWorkerAuthToken();
                        await cacheIndividualDocument({ name: docName, raw_url: rawUrl, pwa_url: pwaUrl });

                        const successMessage = isUpdate ? `Document "${docName}" updated successfully!` : `Document "${docName}" saved offline successfully!`;
                        showNotification('success', successMessage);
                        updateCacheStatusForUrl(rawUrl);
                    } catch (error) {
                        console.error('Failed to save document offline:', error);
                        showNotification('error', `Failed to save "${docName}" offline. Please try again.`);
                    } finally {
                        $btn.html(originalHtml);
                        $btn.prop('disabled', false);
                    }
                });

                async function updateServiceWorkerAuthToken() {
                    try {
                        const response = await fetch('/pwa/auth-token');
                        if (!response.ok) {
                            throw new Error('Failed to fetch auth token');
                        }
                        const data = await response.json();
                        if (navigator.serviceWorker.controller) {
                            navigator.serviceWorker.controller.postMessage({
                                type: 'SET_AUTH_TOKEN',
                                token: data.token
                            });
                        }
                    } catch (error) {
                        console.error('[PWA] Failed to update auth token, caching may fail', error);
                    }
                }

                async function cacheIndividualDocument(docData) {
                    if (!('serviceWorker' in navigator && navigator.serviceWorker.controller)) {
                        throw new Error('Service worker not available');
                    }

                    return new Promise((resolve, reject) => {
                        navigator.serviceWorker.controller.postMessage({
                            type: 'CACHE_PDF',
                            payload: {
                                raw_url: docData.raw_url,
                                pwa_url: docData.pwa_url
                            }
                        });
                        // Assuming success for now, as there is no robust message-back channel
                        resolve();
                    });
                }

                async function updateAllCacheStatus() {
                    const cacheContainers = document.querySelectorAll('[data-cache-container]');
                    for (const container of cacheContainers) {
                        const rawUrl = container.getAttribute('data-raw-url');
                        updateCacheStatusForUrl(rawUrl);
                    }
                }

                async function updateCacheStatusForUrl(rawUrl) {
                    const isCached = await isUrlCached(rawUrl);
                    const container = document.querySelector(`[data-cache-container][data-raw-url="${rawUrl}"]`);
                    const statusEl = document.querySelector(`.cache-status[data-raw-url="${rawUrl}"]`);

                    if (container) {
                        const cacheBtn = container.querySelector('.cache-doc-btn');
                        const updateBtn = container.querySelector('.update-cache-btn');
                        if (isCached) {
                            cacheBtn.style.display = 'none';
                            updateBtn.style.display = 'block';
                            if(statusEl) statusEl.textContent = 'Saved Offline';
                        } else {
                            cacheBtn.style.display = 'block';
                            updateBtn.style.display = 'none';
                            if(statusEl) statusEl.textContent = '';
                        }
                    }
                }

                async function isUrlCached(url) {
                    if (!('caches' in window)) return false;
                    const cache = await caches.open('library-manuals-v1.0.6');
                    return !!(await cache.match(url));
                }

                function showNotification(type, message) {
                    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                    const icon = type === 'success' ? 'mdi-check-circle' : 'mdi-alert-circle';
                    const notification = $(`
                        <div class="alert ${alertClass} alert-dismissible position-fixed" style="top: 20px; right: 20px; z-index: 1060; max-width: 350px;">
                            <i class="mdi ${icon} me-2"></i>
                            ${message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `);
                    $('body').append(notification);
                    setTimeout(() => notification.alert('close'), 5000);
                }
            });
        </script>
    @endpush
@endsection
