<!doctype html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="light-style layout-navbar-fixed layout-compact"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="/storage/assets/"
    data-template="front-pages">
<head>
    <meta charset="utf-8"/>
    <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>

    <title>BinaniAir Library</title>

    <meta name="description" content=""/>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/assets/img/favicon/favicon.png') }}"/>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link
            href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
            rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/fonts/materialdesignicons.css') }}"/>

    <!-- Menu waves for no-customizer fix -->
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/libs/node-waves/node-waves.css') }}"/>

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/css/rtl/core.css') }}"
          class="template-customizer-core-css"/>
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/css/rtl/theme-default.css') }}"
          class="template-customizer-theme-css"/>
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/css/pages/front-page.css') }}"/>
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}"/>
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/libs/typeahead-js/typeahead.css') }}"/>

    {{--    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/libs/apex-charts/apex-charts.css') }}"/>--}}
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/libs/swiper/swiper.css') }}"/>
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/libs/flatpickr/flatpickr.css') }}"/>
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/libs/select2/select2.css') }}"/>


    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/css/pages/front-page-landing.css') }}"/>
    @stack('styles')
    <!-- Helpers -->
    <script src="{{ asset('storage/assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{ asset('storage/assets/vendor/js/template-customizer.js') }}"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('storage/assets/js/config.js') }}"></script>
</head>

<body>

<script src="{{ asset('storage/assets/vendor/js/dropdown-hover.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/js/mega-dropdown.js') }}"></script>
@php
    $user = Auth::user();
@endphp
<!-- Layout wrapper -->
<div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
    <div class="layout-container">
        <!-- Navbar -->
        <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
            <div class="container-xxl">
                <div class="navbar-brand app-brand d-none d-xl-flex py-0 me-4">
                    @if($user->hasRole(['SuperAdmin', 'admin']))
                        <a href="{{ route('home') }}" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                              <span style="color: var(--bs-primary)">
                                  <img width="50" height="50" src="{{ asset('storage/assets/img/logo.png') }}">
                              </span>
                            </span>
                            <span class="app-brand-text demo menu-text fw-bold">Library</span>
                        </a>

                        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                            <i class="mdi mdi-close align-middle"></i>
                        </a>
                    @else
                        <a href="{{ route('manual.index') }}" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                              <span style="color: var(--bs-primary)">
                                  <img width="50" height="50" src="{{ asset('storage/assets/img/logo.png') }}">
                              </span>
                            </span>
                            <span class="app-brand-text demo menu-text fw-bold">Library</span>
                        </a>

                        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                            <i class="mdi mdi-close align-middle"></i>
                        </a>
                    @endif
                </div>

                <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                        <i class="mdi mdi-menu mdi-24px"></i>
                    </a>
                </div>

                <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                    <ul class="navbar-nav flex-row align-items-center ms-auto">

                        <!-- Style Switcher -->
                        <li class="nav-item dropdown-style-switcher dropdown me-1 me-xl-0">
                            <a
                                    class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
                                    href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                <i class="mdi mdi-24px"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
                                        <span class="align-middle"><i
                                                    class="mdi mdi-weather-sunny me-2"></i>Light</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
                                        <span class="align-middle"><i class="mdi mdi-weather-night me-2"></i>Dark</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
                                        <span class="align-middle"><i class="mdi mdi-monitor me-2"></i>System</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- / Style Switcher-->

                        <!-- User -->
                        <li class="nav-item navbar-dropdown dropdown-user dropdown">
                            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                               data-bs-toggle="dropdown">
                                <div class="avatar avatar-online">
                                    <img src="{{ asset('storage/assets/img/avatars/1.png') }}" alt
                                         class="w-px-40 h-auto rounded-circle"/>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar avatar-online">
                                                    <img src="{{ asset('storage/assets/img/avatars/1.png') }}" alt
                                                         class="w-px-40 h-auto rounded-circle"/>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <span
                                                        class="fw-medium d-block"> {{ !empty(Auth::user()->name)?Auth::user()->name:''  }} - ({{ ucfirst(\Illuminate\Support\Facades\Auth::user()->roles->first()->name) }})</span>
                                                <small class="text-muted">
                                                    {{ \Illuminate\Support\Facades\Auth::user()->email }}
                                                </small>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <div class="dropdown-divider"></div>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile') }}">
                                        <i class="mdi mdi-account-outline me-2"></i>
                                        <span class="align-middle">My Profile</span>
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();" target="_blank">
                                        <i class="mdi mdi-logout me-2"></i>
                                        <span class="align-middle">Log Out</span>
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                        <!--/ User -->
                    </ul>
                </div>
            </div>
        </nav>

        <!-- / Navbar -->

                <!-- Layout container -->
        <div class="layout-page">
            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Menu -->
                <aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
                    <div class="container-xxl d-flex h-100">
                        <ul class="menu-inner">
                            @if($user->hasRole(['super-admin','SuperAdmin', 'admin']))
                                <!-- Dashboards -->
                                <li class="menu-item @if(request()->is('home')) {{ __('active') }} @endif">
                                    <a href="{{ route('home') }}" class="menu-link">
                                        <i class="menu-icon tf-icons mdi mdi-home-outline"></i>
                                        <div data-i18n="Home">Home</div>
                                    </a>
                                </li>
                            @endif
                            <li class="menu-item @if(request()->is('manuals')) {{ __('active') }} @endif @if(request()->is('manual/add')) {{ __('active') }} @endif">
                                <a href="{{ route('manual.index') }}" class="menu-link">
                                    <i class="menu-icon tf-icons mdi mdi-book-account"></i>
                                    <div data-i18n="Manuals">Manuals</div>
                                </a>
                            </li>
                            @if($user->hasRole(['super-admin', 'admin', 'librarian']))
                                {{--                                <li class="menu-item @if(request()->is('issue/books')) {{ __('active') }} @endif @if(request()->is('issue/books/add')) {{ __('active') }} @endif">--}}
                                {{--                                    <a href="{{ route('issue.books.index') }}" class="menu-link">--}}
                                {{--                                        <i class="menu-icon tf-icons mdi mdi-book-plus"></i>--}}
                                {{--                                        <div data-i18n="Book Issue">Book Issue</div>--}}
                                {{--                                    </a>--}}
                                {{--                                </li>--}}

                                <li class="menu-item @if(request()->is('users')) {{ __('active') }} @endif @if(request()->is('users/add')) {{ __('active') }} @endif">
                                    <a href="{{ route('users.index') }}" class="menu-link">
                                        <i class="menu-icon tf-icons mdi mdi-account-group"></i>
                                        <div data-i18n="Users">Users</div>
                                    </a>
                                </li>

                                @if($user->hasRole(['super-admin']))
                                    <li class="menu-item">
                                        <a class="menu-link menu-toggle">
                                            <i class="menu-icon tf-icons mdi mdi-apps"></i>
                                            <div data-i18n="Settings">Settings</div>
                                        </a>
                                        <ul class="menu-sub">

                                            <li class="menu-item @if(request()->is('roles')) {{ __('active') }} @endif @if(request()->is('roles/create')) {{ __('active') }} @endif">
                                                <a href="{{ route('roles') }}" class="menu-link">
                                                    <i class="menu-icon tf-icons mdi mdi-apps"></i>
                                                    <div data-i18n="Roles">Roles</div>
                                                </a>
                                            </li>

                                            <li class="menu-item @if(request()->is('permissions')) {{ __('active') }} @endif @if(request()->is('permissions/create')) {{ __('active') }} @endif">
                                                <a href="{{ route('permissions') }}" class="menu-link">
                                                    <i class="menu-icon tf-icons mdi mdi-apps"></i>
                                                    <div data-i18n="Permissions">Permissions</div>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>

                                @endif
                            @endif
                        </ul>
                    </div>
                </aside>
                <!-- / Menu -->

                @yield('content')


                <!-- Footer -->
                <footer class="content-footer footer bg-footer-theme">
                    <div class="container-xxl">
                        <div
                                class="footer-container d-flex align-items-center justify-content-between py-3 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                © 2024
                                @if(date('Y') > 2024)
                                    -
                                    <script>
                                        document.write(new Date().getFullYear());
                                    </script>
                                @endif

                                All rights reserved
                            </div>
                        </div>
                    </div>
                </footer>
                <!-- / Footer -->

                <div class="content-backdrop fade"></div>
            </div>
            <!--/ Content wrapper -->
        </div>

        <!--/ Layout container -->
    </div>
</div>

<!-- Overlay -->
<div class="layout-overlay layout-menu-toggle"></div>

<!-- Drag Target Area To SlideIn Menu On Small Screens -->
<div class="drag-target"></div>
<script src="{{ asset('storage/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/libs/node-waves/node-waves.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/libs/hammer/hammer.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/libs/i18n/i18n.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/js/menu.js') }}"></script>
<!-- endbuild -->

<!-- Vendors JS -->
<script src="{{ asset('storage/assets/vendor/libs/cleavejs/cleave.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/libs/select2/select2.js') }}"></script>

<script src="{{ asset('storage/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/3.0.1/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.colVis.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.html5.min.js"></script>
<!-- Main JS -->
<script src="{{ asset('storage/assets/js/main.js') }}"></script>
@stack('scripts')

</body>
</html>
