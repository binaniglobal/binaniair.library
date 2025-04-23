@extends('layouts.app')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home /</span> My Profile</h4>

        <div class="row">
            <div class="col-md-12">
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
                    @if(session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    <h4 class="card-header">Profile Details</h4>
                    <!-- Account -->
                    <div class="card-body pt-0 mt-1">
                        <form id="formAccountSettings" action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row mt-2 gy-4">
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline">
                                        <input
                                            class="form-control"
                                            type="text"
                                            id="firstName"
                                            value="{{ $User['name'] }}" readonly
                                            autofocus/>
                                        <label for="firstName">First Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="text" name="lastName" readonly
                                               value="{{ $User['surname'] }}"/>
                                        <label for="lastName">Last Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline">
                                        <input
                                            class="form-control"
                                            type="text"
                                            id="email" readonly
                                            value="{{ $User['email'] }}"
                                            placeholder="Email Address"/>
                                        <label for="email">E-mail</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                            <input
                                                type="number"
                                                id="phoneNumber"
                                                name="phone"
                                                class="form-control"
                                                value="{{ $User['phone'] }}"
                                                placeholder="Phone Number"/>
                                            <label for="phoneNumber">Phone Number</label>
                                        </div>
                                        <span class="input-group-text">NG (+234)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary me-2">Save changes</button>
                            </div>
                        </form>
                    </div>
                    <!-- /Account -->
                </div>

                <div class="card mb-4">
                    <h5 class="card-header">Change Password</h5>
                    <div class="card-body">
                        <form id="formAccountSettings" action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="mb-3 col-md-6 form-password-toggle">
                                    <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                            <input
                                                class="form-control"
                                                type="password"
                                                name="current_password"
                                                id="currentPassword"
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"/>
                                            <label for="currentPassword">Current Password</label>
                                        </div>
                                        <span class="input-group-text cursor-pointer"
                                        ><i class="mdi mdi-eye-off-outline"></i
                                            ></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6 form-password-toggle">
                                    <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                            <input
                                                class="form-control"
                                                type="password"
                                                id="newPassword"
                                                name="password"
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"/>
                                            <label for="newPassword">New Password</label>
                                        </div>
                                        <span class="input-group-text cursor-pointer"
                                        ><i class="mdi mdi-eye-off-outline"></i
                                            ></span>
                                    </div>
                                </div>
                                <div class="col-md-6 form-password-toggle">
                                    <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                            <input
                                                class="form-control"
                                                type="password"
                                                name="password_confirmation"
                                                id="confirmPassword"
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"/>
                                            <label for="confirmPassword">Confirm New Password</label>
                                        </div>
                                        <span class="input-group-text cursor-pointer"
                                        ><i class="mdi mdi-eye-off-outline"></i
                                            ></span>
                                    </div>
                                </div>
                            </div>
                            <h6 class="text-body">Password Requirements:</h6>
                            <ul class="ps-3 mb-0">
                                <li class="mb-1">Minimum 8 characters long - the more, the better</li>
                                <li class="mb-1">At least one lowercase character</li>
                                <li>At least one number, symbol, or whitespace character</li>
                            </ul>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary me-2">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{--                <div class="card mb-4">--}}
                {{--                    <h5 class="card-header">Two-steps verification</h5>--}}
                {{--                    <div class="card-body">--}}
                {{--                        <h5 class="mb-3">Two factor authentication is not enabled yet.</h5>--}}
                {{--                        <p class="w-75">--}}
                {{--                            Two-factor authentication adds an additional layer of security to your account by requiring more--}}
                {{--                            than just a password to log in.--}}
                {{--                            <a href="javascript:void(0);">Learn more.</a>--}}
                {{--                        </p>--}}
                {{--                        <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#enableOTP">--}}
                {{--                            Enable two-factor authentication--}}
                {{--                        </button>--}}
                {{--                    </div>--}}
                {{--                </div>--}}
            </div>
        </div>
    </div>
    <!--/ Content -->

@endsection
