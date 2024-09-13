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
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home /</span> Issue Books</h4>

        <!-- Responsive Datatable -->
        <div class="card">
            <h5 class="card-header">Issues</h5>
            <div class="card-datatable table-responsive text-nowrap">
                <table class="dt-responsive table table-hover" id="dt-responsive">
                    <thead>
                    <tr>
                        {{--                        <th>S/N</th>--}}
                        <th>Borrower</th>
                        <th>Manual Name</th>
                        <th>Date Issued</th>
                        <th>Expected Date Returned</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @foreach($Issue as $issue)
                        @php
                        $userName = new \App\Http\Controllers\UserController();
                        $manualName = new \App\Http\Controllers\ManualsController();
                        @endphp
                        <tr>
                            <td>{{ $userName->getUserName($issue->user_id) }}</td>
                            <td>{{ $manualName->getManualName($issue->manual_id) }}</td>
                            <td>@if($manual->status == 0) {{ __('Active') }} @else {{ __('Disable/Banned') }}@endif</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                        <i class="mdi mdi-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#"
                                        ><i class="mdi mdi-pencil-outline me-1"></i> Edit</a
                                        >
                                        <a class="dropdown-item" href="#"
                                        ><i class="mdi mdi-trash-can-outline me-1"></i> Delete</a
                                        >
                                    </div>
                                </div>
                            </td>
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
                    {
                        html: '<a class="btn btn-primary" href="{{ route('issue.books.show') }}"><span class="fa fa-plus-circle" aria-hidden="true"></span> ' +
                            ' <i class="menu-icon tf-icons mdi mdi-book-account"></i>' +
                            ' </a>',
                        attr: {
                            class: 'btn btn-primary'
                        },
                    }
                ],
                "language": {
                    "emptyTable": "<span class='text-success'>Empty table</span>"
                }
            }).buttons().container().appendTo('#dt-responsive_wrapper .col-md-6:eq(0)');
        </script>
    @endpush
@endsection
