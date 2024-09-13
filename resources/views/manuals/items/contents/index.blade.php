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
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home /</span> Manuals Items Content</h4>
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
            <h5 class="card-header">Manual Items Content for {{ ucfirst($Manual->name)? ucfirst($Manual->name): ''}}</h5>
            <div class="card-datatable table-responsive text-nowrap">
                <table class="dt-responsive table table-hover" id="dt-responsive">
                    <thead>
                    <tr>
                        <th>Manuals of {{ $Manual->name }}</th>
                        <th>File Type</th>
                        <th>Size</th>
                        @if($user->hasRole(['super-admin', 'admin', 'librarian']))
                            <th>Action</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @foreach($Items as $items)
                        <tr>
                            <td>
                                <a href="{{ asset(str_replace('public/', 'storage/',$items->link)) }}">{{ $items->name }}</a>
                            </td>
                            <td>{{ $items->file_type=='application/pdf'?'PDF':$items->file_type }}</td>
                            <td>{{ $size->formatBytes($items->file_size) }}</td>
                            @if($user->hasRole(['super-admin', 'admin', 'librarian']))
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                            <i class="mdi mdi-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item"
                                               href="{{ route('manual.items.content.destroy', ['id'=>$items->manual_uid, 'ids'=>$items->micd]) }}"
                                            ><i class="mdi mdi-trash-can-outline me-1"></i> Delete</a
                                            >
                                        </div>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!--/ Responsive Datatable -->
    </div>
    @push('scripts')
        <script>
            $("#dt-responsive").DataTable({
                "responsive": true, "lengthChange": false, "autoWidth": false,
                buttons: [
                    'pageLength',
                        @if($user->hasRole(['super-admin', 'admin', 'librarian']))
                    {
                        html: '<a class="btn btn-primary" href="{{ route('manual.items.content.show', $Id) }}"><span class="fa fa-plus-circle" aria-hidden="true"></span> ' +
                            ' <i class="menu-icon tf-icons mdi mdi-book-account"></i>' +
                            ' </a>',
                        attr: {
                            class: 'btn btn-primary'
                        },
                    }
                    @endif
                ],
                "language": {
                    "emptyTable": "<span class='text-success'>Empty table</span>"
                }
            }).buttons().container().appendTo('#dt-responsive_wrapper .col-md-6:eq(0)');
        </script>
    @endpush
@endsection
