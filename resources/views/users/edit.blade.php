@extends('layouts.app')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('storage/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}"/>
    @endpush

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Users/</span> Edit Users</h4>

        <div class="row">
            <!-- Form Separator -->
            <div class="col-xxl mx-auto">
                <div class="card mb-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <h5 class="card-header">Edit Users</h5>
                    <form class="card-body overflow-hidden" action="{{ route('users.update', $Edit['uuid']) }}"
                          method="POST">
                        @csrf
                        @method('PUT')
                        <h6>1. Account Details</h6>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label" for="first_name">First Name</label>
                            <div class="col-sm-9">
                                <input type="text" id="first_name" value="{{ $Edit['name'] }}"
                                       class="form-control" readonly
                                       placeholder="John"/>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label" for="last_name">Last Name</label>
                            <div class="col-sm-9">
                                <input type="text" id="last_name" value="{{ $Edit['surname'] }}"
                                       class="form-control" readonly
                                       placeholder="Doe"/>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label" for="email">Email</label>
                            <div class="col-sm-9">
                                <div class="input-group input-group-merge">
                                    <input
                                        type="text"
                                        id="email"
                                        name="email"
                                        class="form-control"
                                        placeholder="john.doe"
                                        aria-label="john.doe" readonly
                                        value="{{ Str::before($Edit['email'], '@') }}"
                                        aria-describedby="email"/>
                                    <span class="input-group-text" id="email">@binaniair.com</span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label" for="last_name">Phone Number</label>
                            <div class="col-sm-9">
                                <input type="text" id="phone" name="phone" value="{{ $Edit['phone'] }}"
                                       class="form-control"
                                       placeholder="+23481000000"/>
                            </div>
                        </div>

                        <hr class="my-4 mx-n4 m-auto"/>

                        <h6>2. Role & Permissions
                        </h6>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label" for="role">Role</label>
                            <div class="col-sm-9">
                                <div class="form-floating form-floating-outline">
                                    <select id="role" name="role" class="selectpicker w-100" data-style="btn-default">
                                        @foreach($Roles as $role)
                                            <option value="{{$role->uuid}}" {{ in_array($role->uuid, $AssignedRoles) ? 'selected': '' }}>
                                             {{ strtoupper($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="role">Role</label>
                                </div>
                            </div>
                        </div>


                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label" for="permission">Permissions</label>
                            <!-- Select / Deselect All -->
                            <div class="col-sm-9">
                                <div class="form-floating form-floating-outline">
                                    <select
                                        id="permission"
                                        name="permission[]"
                                        class="select2 form-select form-select-lg"
                                        data-style="btn-default"
                                        multiple
                                        data-actions-box="true">
                                        @foreach($Permissions as $permission)
                                            <option value="{{ $permission->uuid }}"
                                                {{ in_array($permission->uuid, $AssignedPermissions) ? 'selected' : '' }}>
                                            {{ strtoupper($permission->name) }}
                                        @endforeach
                                    </select>
                                    <label for="permission">Select / Deselect All</label>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4">
                            <div class="row justify-content-end">
                                <div class="col-sm-9">
                                    <button type="submit" class="btn btn-primary me-sm-2 me-1">Submit</button>
                                    <a onclick="history.back()" class="btn btn-outline-secondary">Back</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- Page JS -->
        <script src="{{ asset('storage/assets/js/form-layouts.js') }}"></script>
        <script src="{{ asset('storage/assets/vendor/libs/select2/select2.js') }}"></script>
        <script src="{{ asset('storage/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
        <script src="{{ asset('storage/assets/js/forms-selects.js') }}"></script>
        {{--        <script src="{{ asset('storage/') }}"></script>--}}
    @endpush
@endsection
