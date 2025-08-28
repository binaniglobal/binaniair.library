@php use App\Models\Manuals; use Illuminate\Support\Facades\Auth; @endphp
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
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home / <a href="{{ route('manual.index') }}">Manuals</a></span>
            / {{ ucfirst($Manual->name) }}</h4>
        @php
            $user = Auth::user();
            $size = new \App\Http\Controllers\ManualsItemController();
        @endphp
            <!-- Responsive Datatable -->
        <div class="card">
            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            <h5 class="card-header">{{ ucfirst($Manual->name) }}</h5>
            <div class="card-datatable table-responsive text-nowrap">
                <table class="dt-responsive table table-hover" id="dt-responsive">
                    <thead>
                    <tr>
                        <th>{{ $Manual->name }}</th>
                        <th>File Type</th>
                        <th>File Size</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @can('view-manual')
                        @foreach($Items as $items)
                            @php
                                $count = countManualItemsById($items->miid);
                                $parentManual = Manuals::where('mid', $items->manual_uid)->first();
                            @endphp
                            @can('access-manual-'.$parentManual->name.'.'.$items->name)
                                <tr>
                                    @if($items->file_type == 'Folder')
                                        <td>
                                            <a href="{{ route('manual.items.content.index', $items->miid) }}">{{ $items->name }}</a>
                                        </td>
                                    @else
                                        <td>
                                            <div class="btn-group">
                                                @if($items->file_type === 'application/pdf')
                                                    <a class="btn btn-link p-0 text-start pdf-modal-trigger"
                                                       href="{{ route('manual.items.view', ['filename' => $items->link]) }}">
                                                        <i class="mdi mdi-file-pdf-box text-danger me-1"></i>
                                                        {{ $items->name }}
                                                    </a>
                                                @else
                                                    <a href="{{ route('download.submanuals',$items->link) }}">{{ $items->name }}</a>
                                                @endif
                                            </div>
                                        </td>
                                    @endif

                                    <td>{{ $items->file_type === 'application/pdf'?'PDF':'' }}</td>
                                    <td>{{ $items->file_type === 'application/pdf' ? $size->formatBytes($items->file_size):'' }}</td>


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
                                                       data-doc-name="{{ $items->name }}"
                                                       data-raw-url="{{ route('manual.items.raw', ['filename' => $items->link]) }}"
                                                       data-pwa-url="{{ getPwaSubManualUrl($items->link) }}">
                                                        <i class="mdi mdi-download me-1"></i> Cache Offline
                                                    </a>
                                                @endif
                                                @can('destroy-manual')
                                                    <a class="dropdown-item"
                                                       href="{{ route('manual.items.destroy', ['id'=>$items->manual_uid, 'ids'=>$items->miid]) }}">
                                                        <i class="mdi mdi-trash-can-outline me-1"></i> Delete</a>
                                                @endcan
                                            </div>
                                        </div>
                                    </td>

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
                $("#dt-responsive").DataTable({
                    "responsive": true, "lengthChange": false, "autoWidth": false, ordering: false,
                    buttons: [
                        'pageLength'
                        @can('create-manual')
                        ,
                        {
                            html: '<a class="btn btn-primary" href="{{ route('manual.items.show', $Id) }}"><span class="fa fa-plus-circle" aria-hidden="true"></span> ' +
                                ' <i class="menu-icon tf-icons mdi mdi-book-account"></i>' +
                                ' <i class="menu-icon tf-icons mdi mdi-file-content"></i>' +
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
                    $('#pdf-iframe').attr('src', pdfUrl);
                    $('#pdfViewerModal').modal('show');
                });

                // Clear iframe src when modal is hidden to stop loading
                $('#pdfViewerModal').on('hidden.bs.modal', function () {
                    $('#pdf-iframe').attr('src', '');
                });

                // Individual document caching functionality
                $(document).on('click', '.cache-doc-btn', function (e) {
                    e.preventDefault();

                    const $btn = $(this);
                    const docName = $btn.data('doc-name');
                    const rawUrl = $btn.data('raw-url');
                    const pwaUrl = $btn.data('pwa-url');

                    const originalHtml = $btn.html();
                    $btn.html('<i class="mdi mdi-loading mdi-spin"></i> Caching...');
                    $btn.prop('disabled', true);

                    cacheIndividualDocument({ name: docName, raw_url: rawUrl, pwa_url: pwaUrl })
                        .then(() => {
                            showNotification('success', `Document "${docName}" cached successfully!`);
                            $btn.html('<i class="mdi mdi-check"></i> Cached');
                            $btn.removeClass('btn-outline-primary').addClass('btn-success');
                            $btn.prop('disabled', false);
                            setTimeout(() => {
                                $btn.html(originalHtml);
                                $btn.removeClass('btn-success').addClass('btn-outline-primary');
                            }, 3000);
                        })
                        .catch(error => {
                            console.error('Failed to cache document:', error);
                            showNotification('error', `Failed to cache "${docName}". Please try again.`);
                            $btn.html(originalHtml);
                            $btn.prop('disabled', false);
                        });
                });

                async function cacheIndividualDocument(docData) {
                    if (!('serviceWorker' in navigator && navigator.serviceWorker.controller)) {
                        throw new Error('Service worker not available');
                    }

                    return new Promise((resolve, reject) => {
                        const messageChannel = new MessageChannel();
                        messageChannel.port1.onmessage = event => {
                            if (event.data.success) {
                                resolve();
                            } else {
                                reject(new Error(event.data.error || 'Caching failed in service worker'));
                            }
                        };

                        navigator.serviceWorker.controller.postMessage({
                            type: 'CACHE_PDF',
                            payload: {
                                raw_url: docData.raw_url,
                                pwa_url: docData.pwa_url
                            }
                        }, [messageChannel.port2]);
                    });
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
