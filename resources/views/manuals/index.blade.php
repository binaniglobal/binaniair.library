@php use App\Models\Manuals;use Illuminate\Support\Facades\Auth; @endphp
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
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home /</span> Manuals</h4>

        @php
            $user = Auth::user();
        @endphp
            <!-- Responsive Datatable -->
        <div class="card">
            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            <h5 class="card-header">Manuals</h5>
            <div class="card-datatable table-responsive text-nowrap">
                <table class="dt-responsive table table-hover" id="dt-responsive">
                    <thead>
                    <tr>
                        <th>Manuals</th>
                        {{--                        <th>No of Folders</th>--}}
                        @can('destroy-manual')
                            <th>Action</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @can('view-manual')
                        @foreach($Manuals as $manual)
                            @php
                                $manualCount = \App\Models\ManualsItem::where('manual_uid', $manual->mid)->get();

                            @endphp
                            @can('access-manual-' . $manual->name)
                                <tr>
                                    @if($manual->type == 0)
                                        <td>
                                            <a href="{{ route('manual.items.index', $manual->mid) }}">{{ $manual->name }}</a>
                                        </td>
                                    @else
                                        <td>{{ $manual->name }}
                                        </td>
                                    @endif
                                    {{--                                    <td>{{ $manualCount->item->count() }}</td>--}}
                                    @can('destroy-manual')
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                        data-bs-toggle="dropdown">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item"
                                                       href="{{ route('manual.destroy', ['id' => $manual->mid]) }}">
                                                        <i class="mdi mdi-trash-can-outline me-1"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    @endcan
                                </tr>
                            @endcan
                        @endforeach
                    @else
                        <span>You do not have permission to view any manual.</span>
                    @endcan
                    </tbody>
                </table>
            </div>
        </div>
        <!--/ Responsive Datatable -->
    </div>
    @push('scripts')
        <script>
            // Initialize DataTable with offline support
            const dataTable = $("#dt-responsive").DataTable({
                "responsive": true, "lengthChange": false, "autoWidth": false,
                buttons: [
                    'pageLength'
                    @can('create-manual')
                    ,
                    {
                        html: '<a class="btn btn-primary" href="{{ route('manual.add') }}"><span class="fa fa-plus-circle" aria-hidden="true"></span> ' +
                            ' <i class="menu-icon tf-icons mdi mdi-book-account"></i>' +
                            ' </a>',
                        attr: {
                            class: 'btn btn-primary'
                        },
                    }
                    @endcan
                    , {
                        text: '<i class="mdi mdi-download"></i> Cache for Offline',
                        className: 'btn btn-info btn-sm',
                        action: function () {
                            cacheAllManuals();
                        }
                    }

                ],
                "language": {
                    "emptyTable": "<span class='text-success'>Empty table</span>"
                }
            }).buttons().container().appendTo('#dt-responsive_wrapper .col-md-6:eq(0)');

            // Cache all manuals for offline access
            async function cacheAllManuals() {
                if (!window.libraryStorage) {
                    alert('Offline storage is not available');
                    return;
                }

                try {
                    // Show loading indicator
                    const loadingBtn = $('.btn:contains("Cache for Offline")');
                    const originalText = loadingBtn.html();
                    loadingBtn.html('<i class="mdi mdi-loading mdi-spin"></i> Caching...');
                    loadingBtn.prop('disabled', true);

                    // Use server-side data directly from controller
                    const manuals = @json($AccessibleManuals);

                    console.log('Server-side manuals data:', manuals);
                    console.log('Total manuals found:', manuals.length);

                    // Store manuals in IndexedDB
                    for (const manual of manuals) {
                        await window.libraryStorage.storeManual(manual);
                    }

                    // Debug output
                    console.log('=== MANUAL CACHING DEBUG ===');
                    console.log('Manuals found:', manuals.length);
                    console.log('Service worker support:', 'serviceWorker' in navigator);
                    console.log('Service worker controller:', navigator.serviceWorker ? navigator.serviceWorker.controller : null);
                    console.log('Manuals data:', manuals);

                    // Send message to service worker to cache pages
                    if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
                        console.log('Sending cache message to service worker with', manuals.length, 'manuals');
                        navigator.serviceWorker.controller.postMessage({
                            type: 'CACHE_MANUAL',
                            manuals: manuals
                        });

                        // Also manually call the API to test
                        await testManualApis(manuals);
                    } else {
                        console.warn('Service worker not available or not controlling this page');
                        console.log('Attempting manual API test instead...');
                        await testManualApis(manuals);
                    }

                    // Show success message
                    showNotification('success', `Successfully cached ${manuals.length} manuals for offline access!`);

                    // Reset button
                    loadingBtn.html(originalText);
                    loadingBtn.prop('disabled', false);

                } catch (error) {
                    console.error('Failed to cache manuals:', error);
                    showNotification('error', 'Failed to cache manuals. Please try again.');

                    // Reset button
                    const loadingBtn = $('.btn:contains("Caching...")');
                    loadingBtn.html('<i class="mdi mdi-download"></i> Cache for Offline');
                    loadingBtn.prop('disabled', false);
                }
            }

            // Test manual APIs directly
            async function testManualApis(manuals) {
                console.log('=== TESTING MANUAL APIS ===');

                for (const manual of manuals.slice(0, 2)) { // Test only first 2 manuals
                    try {
                        console.log(`Testing API for manual ${manual.id}...`);

                        // Test manual items API
                        const itemsResponse = await fetch(`/api/manual/${manual.id}/items`);
                        console.log(`Manual ${manual.id} items API:`, itemsResponse.status, itemsResponse.ok);

                        if (itemsResponse.ok) {
                            const itemsData = await itemsResponse.json();
                            console.log(`Manual ${manual.id} items:`, itemsData);
                        } else {
                            console.error(`Failed to fetch items for manual ${manual.id}:`, await itemsResponse.text());
                        }

                    } catch (error) {
                        console.error(`Error testing manual ${manual.id}:`, error);
                    }
                }
            }

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

            // Load offline data if network is unavailable
            async function loadOfflineData() {
                if (!navigator.onLine && window.libraryStorage) {
                    try {
                        const offlineManuals = await window.libraryStorage.getAllManuals();

                        if (offlineManuals.length > 0) {
                            // Clear current table
                            dataTable.clear();

                            // Add offline data to table
                            offlineManuals.forEach(manual => {
                                const row = [
                                    `<a href="/manual/sub-manuals/${manual.id}" class="text-muted">${manual.name} <small>(Offline)</small></a>`,
                                    @can('destroy-manual')
                                        '<span class="text-muted">-</span>'
                                    @endcan
                                ];
                                dataTable.row.add(row);
                            });

                            dataTable.draw();

                            // Show offline indicator
                            showNotification('info', `Showing ${offlineManuals.length} cached manuals (offline mode)`);
                        }
                    } catch (error) {
                        console.error('Failed to load offline data:', error);
                    }
                }
            }

            // Check if we're offline and load cached data
            $(document).ready(function () {
                // Wait for storage to initialize
                setTimeout(() => {
                    loadOfflineData();
                }, 1000);

                // Listen for online/offline events
                window.addEventListener('offline', () => {
                    setTimeout(loadOfflineData, 500);
                });

                window.addEventListener('online', () => {
                    // Reload page when coming back online
                    setTimeout(() => {
                        if (window.location.pathname === '/manuals') {
                            window.location.reload();
                        }
                    }, 1000);
                });
            });
        </script>
    @endpush
@endsection
