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

        <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.css"/>
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.css">
    @endpush
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home /</span> Users</h4>

        <!-- Responsive Datatable -->
        <div class="card">
            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            <h5 class="card-header">Users</h5>

            <div class="card-datatable table-responsive text-nowrap">
                <table class="dt-responsive table table-hover" id="dt-responsive">
                    <thead>
                    <tr>
{{--                        <th>S/N</th>--}}
                        <th>Names</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        @canany(['edit-user','destroy-user'])
                            <th>Action</th>
                        @endcanany
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @can('view-user')
                        @foreach($Users as $index => $user)
                            <tr>
{{--                                <td>{{ $index+1 }}</td>--}}
                                <td>{{ ucfirst($user->name).' '. ucfirst($user->surname) }}</td>
                                <td>{{ ucfirst($user->email) }}</td>
                                <td>{{ $user->phone}}</td>
                                <td>{{ ucfirst($user->load('roles')->getRoleNames()->first()) }}</td>
                                @canany(['edit-user', 'destroy-user'])
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                    data-bs-toggle="dropdown">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @can(['edit-user'])
                                                    @if(!in_array($user->getRoleNames()->first(), ['super-admin', 'SuperAdmin']))
                                                        @if($user->uuid !== auth()->user()->uuid)
                                                            <a class="dropdown-item"
                                                               href="{{ route('users.edit', [$user->uuid, \Illuminate\Support\Str::uuid()]) }}">
                                                                <i class="mdi mdi-pencil-outline me-1"></i> Edit</a>
                                                        @endif
                                                    @else
                                                        @if(auth()->user()->hasRole('super-admin'))
                                                            <a class="dropdown-item"
                                                               href="{{ route('users.edit', [$user->uuid, \Illuminate\Support\Str::uuid()]) }}">
                                                                <i class="mdi mdi-pencil-outline me-1"></i> Edit</a>
                                                        @else
                                                            <a class="dropdown-item disabled" href="#"
                                                               onclick="return false;">
                                                                <i class="mdi mdi-pencil-outline me-1"></i> Edit</a>
                                                        @endif
                                                    @endif
                                                @endcan

                                                @can(['reset-password'])
                                                    @if(!in_array($user->getRoleNames()->first(), ['super-admin', 'SuperAdmin']))
                                                        <a class="dropdown-item"
                                                           href="#" onclick="return true;">
                                                            <i class="mdi mdi-circle-edit-outline me-1"></i> Reset
                                                            Password(Feature coming soon)</a>
                                                    @else
                                                        @if(auth()->user()->hasRole('super-admin'))
                                                            <a class="dropdown-item"
                                                               href="#" onclick="return false;">
                                                                <i class="mdi mdi-circle-edit-outline me-1"></i> Reset
                                                                Password(Feature coming soon)</a>
                                                        @endif
                                                    @endif
                                                @endcan

                                                @can(['destroy-user'])
                                                    @if(!in_array($user->getRoleNames()->first(), ['super-admin', 'SuperAdmin', 'admin']))
                                                        <a class="dropdown-item"
                                                           href="{{ route('users.destroy', [$user->uuid, \Illuminate\Support\Str::uuid()]) }}">
                                                            <i class="mdi mdi-trash-can-outline me-1"></i>
                                                            Delete</a>
                                                    @else
                                                        @if(auth()->user()->hasRole('super-admin'))
                                                            <a class="dropdown-item"
                                                               href="{{ route('users.destroy', [$user->uuid, \Illuminate\Support\Str::uuid()]) }}">
                                                                <i class="mdi mdi-trash-can-outline me-1"></i>
                                                                Delete</a>
                                                        @endif
                                                    @endif
                                                @endcan
                                            </div>
                                        </div>
                                    </td>
                                @endcanany
                            </tr>
                        @endforeach
                    @endcan
                    </tbody>
                </table>
            </div>
        </div>
        <!--/ Responsive Datatable -->
    </div>
    @push('scripts')
        {{--     --}}
        <script>

            $(function () {
                $("#dt-responsive").DataTable({
                    "responsive": true, "lengthChange": false, "autoWidth": false,
                    buttons: [
                        'pageLength'
                        @can(['create-user'])
                        ,
                        {
                            html: '<a class="btn btn-primary" href="{{ route('users.add') }}"><span class="fa fa-plus-circle" aria-hidden="true"></span>&nbsp; <i class="fa fa-user"></i></a>',
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

            });
        </script>
    @endpush
@endsection
