@php use App\Models\ManualItemContent;use App\Models\Manuals;use Illuminate\Support\Facades\Auth; @endphp
@extends('layouts.app')

@section('content')
    @include('components.secure-manual-viewer')
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
                                //                                $count = ManualItemContent::where('manual_items_uid', $items->miid)->count();
                                                                $parentManual = Manuals::where('mid', $items->manual_uid)->first();
                            @endphp
                            @can('access-manual-'.$parentManual->name.'.'. $Manual->name.'.'.$items->name)
                                <tr>
                                    <td>
                                        <div class="btn-group">
                                            @if($items->file_type === 'application/pdf')
                                                <button class="btn btn-link p-0 text-start secure-view-btn" 
                                                        onclick="openSecureViewer('{{ $items->micd }}', '{{ $items->name }}')"
                                                        title="View document securely">
                                                    <i class="mdi mdi-shield-check text-success me-1"></i>
                                                    {{ $items->name }}
                                                </button>
                                                &nbsp; &nbsp;
                                                <button class="btn btn-sm btn-outline-success secure-cache-btn"
                                                        data-doc-id="{{ $items->micd }}"
                                                        data-doc-name="{{ $items->name }}"
                                                        onclick="cacheSecureDocument('{{ $items->micd }}', '{{ $items->name }}')"
                                                        title="Cache this document securely for offline access">
                                                    <i class="mdi mdi-shield-lock"></i> Cache
                                                </button>
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
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                        data-bs-toggle="dropdown">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    @if($items->file_type === 'application/pdf')
                                                        <a class="dropdown-item cache-doc-btn"
                                                           href="#"
                                                           data-doc-id="{{ $items->micd }}"
                                                           data-doc-name="{{ $items->name }}"
                                                           data-doc-path="{{ $items->link }}"
                                                           data-pwa-url="{{ getPwaSubManualContentUrl($items->link) }}">
                                                            <i class="mdi mdi-download me-1"></i> Cache Offline
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
    @push('scripts')
        <script>
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

            // Individual document caching functionality
            $(document).on('click', '.cache-doc-btn', function(e) {
                e.preventDefault();

                const $btn = $(this);
                const docId = $btn.data('doc-id');
                const docName = $btn.data('doc-name');
                const docPath = $btn.data('doc-path');
                const pwaUrl = $btn.data('pwa-url');

                console.log('Caching content document:', { docId, docName, docPath, pwaUrl });

                // Show loading state
                const originalHtml = $btn.html();
                $btn.html('<i class="mdi mdi-loading mdi-spin"></i> Caching...');
                $btn.prop('disabled', true);

                // Cache individual document
                cacheIndividualDocument({
                    id: docId,
                    name: docName,
                    file_path: docPath,
                    pwa_url: pwaUrl,
                    file_type: 'application/pdf'
                }).then(() => {
                    showNotification('success', `Document "${docName}" cached successfully!`);

                    // Update button to show cached state
                    $btn.html('<i class="mdi mdi-check"></i> Cached');
                    $btn.removeClass('btn-outline-primary').addClass('btn-success');
                    $btn.prop('disabled', false);

                    // Reset button after 3 seconds
                    setTimeout(() => {
                        $btn.html(originalHtml);
                        $btn.removeClass('btn-success').addClass('btn-outline-primary');
                    }, 3000);

                }).catch(error => {
                    console.error('Failed to cache document:', error);
                    showNotification('error', `Failed to cache "${docName}". Please try again.`);

                    // Reset button
                    $btn.html(originalHtml);
                    $btn.prop('disabled', false);
                });
            });

            // Cache individual document function
            async function cacheIndividualDocument(docData) {
                try {
                    // Store in IndexedDB first
                    if (window.libraryStorage) {
                        await window.libraryStorage.storeManualContent(docData);
                    }

                    // Update auth token first
                    await updateServiceWorkerAuthToken();

                    // Cache via service worker
                    if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
                        return new Promise((resolve, reject) => {
                            const messageChannel = new MessageChannel();

                            messageChannel.port1.onmessage = function(event) {
                                if (event.data.success) {
                                    resolve(event.data);
                                } else {
                                    reject(new Error(event.data.error || 'Caching failed'));
                                }
                            };

                            navigator.serviceWorker.controller.postMessage({
                                type: 'CACHE_INDIVIDUAL_DOCUMENT',
                                document: docData
                            }, [messageChannel.port2]);
                        });
                    } else {
                        throw new Error('Service worker not available');
                    }
                } catch (error) {
                    console.error('Document caching error:', error);
                    throw error;
                }
            }

            // Update service worker authentication token
            async function updateServiceWorkerAuthToken() {
                if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
                    try {
                        const response = await fetch('/pwa/auth-token', {
                            credentials: 'same-origin',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (response.ok) {
                            const tokenData = await response.json();

                            navigator.serviceWorker.controller.postMessage({
                                type: 'SET_AUTH_TOKEN',
                                token: tokenData.token,
                                expires_at: tokenData.expires_at
                            });

                            return true;
                        }
                    } catch (error) {
                        console.error('Failed to update auth token:', error);
                    }
                }
                return false;
            }

            // Secure document caching function
            window.cacheSecureDocument = async function(docId, docName) {
                const $btn = $(`.secure-cache-btn[data-doc-id="${docId}"]`);
                
                try {
                    // Show loading state
                    const originalHtml = $btn.html();
                    $btn.html('<i class="mdi mdi-loading mdi-spin"></i> Caching...');
                    $btn.prop('disabled', true);

                    // Get encryption key
                    const encryptionKey = await window.secureViewer.getUserSessionKey();
                    
                    // Fetch document data from secure API
                    const response = await fetch(`/api/manual-content/${docId}/secure`, {
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`Failed to fetch document: ${response.status}`);
                    }

                    const data = await response.json();
                    
                    if (!data.success) {
                        throw new Error(data.message || 'Failed to fetch document data');
                    }

                    // Encrypt and store the document
                    const encryptedData = await window.secureViewer.encryptData(data.data, encryptionKey);
                    
                    if (window.libraryStorage) {
                        await window.libraryStorage.storeEncryptedManual({
                            id: docId,
                            name: docName,
                            encrypted_data: encryptedData
                        });
                    }

                    // Update button to show success
                    $btn.html('<i class="mdi mdi-check"></i> Cached');
                    $btn.removeClass('btn-outline-success').addClass('btn-success');
                    
                    showNotification('success', `Document "${docName}" cached securely!`);

                    // Reset button after 3 seconds
                    setTimeout(() => {
                        $btn.html(originalHtml);
                        $btn.removeClass('btn-success').addClass('btn-outline-success');
                        $btn.prop('disabled', false);
                    }, 3000);

                } catch (error) {
                    console.error('Failed to cache secure document:', error);
                    showNotification('error', `Failed to cache "${docName}": ${error.message}`);
                    
                    // Reset button
                    const originalHtml = $btn.data('original-html') || '<i class="mdi mdi-shield-lock"></i> Cache';
                    $btn.html(originalHtml);
                    $btn.prop('disabled', false);
                }
            };

            // Show notification function
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

                // Auto-hide after 5 seconds
                setTimeout(() => {
                    notification.alert('close');
                }, 5000);
            }
        </script>
    @endpush
@endsection
