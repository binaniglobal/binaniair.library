@php use App\Models\Manuals; @endphp
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
            <h5 class="card-header">Manuals</h5>
            <div class="card-datatable table-responsive text-nowrap">
                <table class="dt-responsive table table-hover" id="dt-responsive">
                    <thead>
                    <tr>
                        <th>Manuals</th>
                        <th>No of Folders</th>
                        @can('can destroy')
                            <th>Action</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @foreach($Manuals as $manual)
                        @php
                            $manualCount = \App\Models\ManualsItem::where('manual_uid', $manual->mid)->count();

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
                                <td>{{ $manualCount }}</td>
                                {{--                            <td>@if($manual->status == 0)--}}
                                {{--                                    {{ __('Active') }}--}}
                                {{--                                @else--}}
                                {{--                                    {{ __('Disabled') }}--}}
                                {{--                                @endif</td>--}}
                                @can('can destroy')
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                    data-bs-toggle="dropdown">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#">
                                                    <i class="mdi mdi-trash-can-outline me-1"></i> Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @endcan
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
                        html: '<a class="btn btn-primary" href="{{ route('manual.add') }}"><span class="fa fa-plus-circle" aria-hidden="true"></span> ' +
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
