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
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home /</span> Manuals Items</h4>
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
            <h5 class="card-header">Manual Items for {{ ucfirst($Manual->name) }}</h5>
            <div class="card-datatable table-responsive text-nowrap">
                <table class="dt-responsive table table-hover" id="dt-responsive">
                    <thead>
                    <tr>
                        <th>Manuals of {{ $Manual->name }}</th>
                        <th>No of Files</th>
                        <th>Type</th>
                        @if($user->hasRole(['admin', 'librarian']))
                            <th>Action</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @foreach($Items as $items)
                        @php
                            $count = \App\Models\ManualItemContent::where('manual_uid',$items->miid)->count();
                        @endphp
                        <tr>
                            @if($items->file_type == 'Folder')
                                <td>
                                    <a href="{{ route('manual.items.content.index', $items->miid) }}">{{ $items->name }}</a>
                                </td>
                            @else
                                <td>
                                    <a href="{{ asset(str_replace('public/', 'storage/',$items->link)) }}">{{ $items->name }}</a>
                                </td>
                            @endif
                                <td>{{ $count }}</td>
                            <td>{{ $items->file_type }}</td>

                            @if($user->hasRole(['admin', 'librarian']))
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                            <i class="mdi mdi-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            @if($items->file_type == 'Folder')
                                                <a class="dropdown-item"
                                                   href="{{ route('manual.items.edit', ['id'=>$items->miid]) }}"
                                                ><i class="mdi mdi-pencil-outline me-1"></i> Edit</a>
                                            @endif
                                            <a class="dropdown-item"
                                               href="{{ route('manual.items.destroy', ['id'=>$items->manual_uid, 'ids'=>$items->miid]) }}"
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
                        html: '<a class="btn btn-primary" href="{{ route('manual.items.show', $Id) }}"><span class="fa fa-plus-circle" aria-hidden="true"></span> ' +
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
