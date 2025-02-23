@php use Illuminate\Support\Facades\Auth; @endphp
@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home</span></h4>
        @php
            $users = new \App\Http\Controllers\UserController();
            $manuals = new \App\Http\Controllers\ManualsController();
            $booksIssued = new \App\Http\Controllers\IssuingBooksController();
            $roles = \App\Models\Role::whereNotIn('name', ['super-admin'])->count();
            $permission = \App\Models\Permission::count();
        @endphp
        @php
            $user = Auth::user();
        @endphp
            <!-- Card Border Shadow -->
        <div class="row">
            @if($user->hasRole(['SuperAdmin','admin']))
                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card card-border-shadow-primary h-80">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <div class="avatar me-2">
                          <span class="avatar-initial rounded bg-label-primary"
                          ><i class="mdi mdi-account-tie mdi-20px"></i
                              ></span>
                                </div>
                                <h4 class="ms-1 mb-0 display-6">{{ number_format($users->noOfUsers(), 0) }}</h4>
                            </div>
                            <p class="mb-0 text-heading">No of Users</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($user->hasRole(['SuperAdmin']))
                {{--            <div class="col-sm-6 col-lg-3 mb-4">--}}
                {{--                <div class="card card-border-shadow-danger h-80">--}}
                {{--                    <div class="card-body">--}}
                {{--                        <div class="d-flex align-items-center mb-2 pb-1">--}}
                {{--                            <div class="avatar me-2">--}}
                {{--                          <span class="avatar-initial rounded bg-label-danger">--}}
                {{--                            <i class="mdi mdi-bookshelf mdi-20px"></i>--}}
                {{--                          </span>--}}
                {{--                            </div>--}}
                {{--                            <h4 class="ms-1 mb-0 display-6">{{ number_format($booksIssued->noOfIssuingBooks(), 0) }}</h4>--}}
                {{--                        </div>--}}
                {{--                        <p class="mb-0 text-heading">No of Books Issued</p>--}}
                {{--                    </div>--}}
                {{--                </div>--}}
                {{--            </div>--}}

                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card card-border-shadow-info h-80">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <div class="avatar me-2">
                          <span class="avatar-initial rounded bg-label-info"
                          ><i class="mdi mdi-timer-outline mdi-20px"></i
                              ></span>
                                </div>
                                <h4 class="ms-1 mb-0 display-6">{{ number_format($roles, 0) }}</h4>
                            </div>
                            <p class="mb-0 text-heading">No of Roles</p>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card card-border-shadow-success h-80">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <div class="avatar me-2">
                          <span class="avatar-initial rounded bg-label-success"
                          ><i class="mdi mdi-timer-outline mdi-20px"></i
                              ></span>
                                </div>
                                <h4 class="ms-1 mb-0 display-6">{{ number_format($permission, 0) }}</h4>
                            </div>
                            <p class="mb-0 text-heading">No of Permission</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <!--/ Card Border Shadow -->

    </div>
@endsection
