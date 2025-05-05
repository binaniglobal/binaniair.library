<!doctype html>
<html
    lang="en"
    class="light-style layout-wide customizer-hide"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="/storage/assets/"
    data-template="horizontal-menu-template">
<head>
    <meta charset="utf-8"/>
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>

    <title>Confirm Password</title>

    <meta name="description" content=""/>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ getGlobalImage('Normal') }}"/>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
        rel="stylesheet"/>

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/fonts/materialdesignicons.css') }}"/>
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/fonts/flag-icons.css') }}"/>

    <!-- Menu waves for no-customizer fix -->
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/libs/node-waves/node-waves.css') }}"/>

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css"/>
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/css/rtl/theme-default.css') }}"
          class="template-customizer-theme-css"/>
    {{--    <link rel="stylesheet" href="storage/assets/css/demo.css" />--}}

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}"/>
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/libs/typeahead-js/typeahead.css') }}"/>
    <!-- Vendor -->
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/libs/@form-validation/form-validation.css') }}"/>

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/css/pages/page-auth.css') }}"/>

    <!-- Helpers -->
    <script src="{{ asset('storage/assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{ asset('storage/assets/vendor/js/template-customizer.js') }}"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('storage/assets/js/config.js') }}"></script>
</head>

<body>
<!-- Content -->

<div class="position-relative">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">

            <div class="card p-2">
                <!-- Logo -->

                <!-- /Logo -->
                <!-- Reset Password -->
                <div class="card-body">
                    <h4 class="mb-2">{{ __('Confirm Password') }} 🔒</h4>
                    <p class="mb-4">{{ __('Please confirm your password before continuing.') }}</p>
                    <form id="formAuthentication" class="mb-3" method="POST" action="{{ route('password.confirm') }}">
                        @csrf
                        <div class="mb-3 form-password-toggle">
                            <div class="input-group input-group-merge">
                                <div class="form-floating form-floating-outline">
                                    <input
                                        type="password"
                                        id="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" />
                                    <label for="password">{{ __('Password') }}</label>
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <span class="input-group-text cursor-pointer"><i class="mdi mdi-eye-off-outline"></i></span>
                            </div>
                        </div>
                        <button class="btn btn-primary d-grid w-100 mb-3">{{ __('Confirm Password') }}</button>
                        <div class="text-center">
                            <a onclick="history.back()" class="d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-chevron-left scaleX-n1-rtl mdi-24px"></i>
                                Go Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- / Content -->

<!-- Core JS -->
<!-- build:js storage/assets/vendor/js/core.js -->
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
<script src="{{ asset('storage/assets/vendor/libs/@form-validation/popular.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
<script src="{{ asset('storage/assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>

<!-- Main JS -->
<script src="{{ asset('storage/assets/js/main.js') }}"></script>

<!-- Page JS -->
<script src="{{ asset('storage/assets/js/pages-auth.js') }}"></script>
</body>
</html>


