@extends('layouts.app')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Users/</span> Edit Users</h4>

        <div class="row">
            <!-- Form Separator -->
            <div class="col-xxl">
                <div class="card mb-4">
                    <h5 class="card-header">Edit Users</h5>
                    <form class="card-body overflow-hidden" action="#">
                        <h6>1. Account Details</h6>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label" for="first_name">First Name</label>
                            <div class="col-sm-9">
                                <input type="text" id="first_name" name="first_name" value="{{ $Edit['name'] }}"
                                       class="form-control"
                                       placeholder="John"/>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label" for="last_name">Last Name</label>
                            <div class="col-sm-9">
                                <input type="text" id="last_name" name="last_name" value="{{ $Edit['surname'] }}"
                                       class="form-control"
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
                                        aria-label="john.doe"
                                        value="{{ $Edit['email'] }}"
                                        aria-describedby="email"/>
                                    {{--                                    <span class="input-group-text" id="email">@binaniair.com</span>--}}
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
                        <hr class="my-4 mx-n4"/>
                        <h6>2. Roles
                            {{--                            and Permissions--}}
                        </h6>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label" for="role">Role</label>
                            <div class="col-sm-9">
                                <select id="role" name="role" class="select2 form-select" data-allow-clear="true">
                                    @foreach($Roles as $role)
                                        <option value="{{ $role->name }}">{{ var_dump($role->name) }}</option>
                                    @endforeach
                                </select>
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
    @endpush
@endsection
