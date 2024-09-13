<!doctype html>

<html
    lang="en"
    class="light-style layout-wide customizer-hide"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="storage/assets/"
    data-template="horizontal-menu-template">
<head>
    <meta charset="utf-8"/>
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>

    <title>Login Basic - Pages | Materialize - Material Design HTML Admin Template</title>

    <meta name="description" content=""/>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="storage/assets/img/favicon/favicon.ico"/>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
        rel="stylesheet"/>

    <!-- Icons -->
    <link rel="stylesheet" href="storage/assets/vendor/fonts/materialdesignicons.css"/>
    <link rel="stylesheet" href="storage/assets/vendor/fonts/flag-icons.css"/>

    <!-- Menu waves for no-customizer fix -->
    <link rel="stylesheet" href="storage/assets/vendor/libs/node-waves/node-waves.css"/>

    <!-- Core CSS -->
    <link rel="stylesheet" href="storage/assets/vendor/css/rtl/core.css" class="template-customizer-core-css"/>
    <link rel="stylesheet" href="storage/assets/vendor/css/rtl/theme-default.css"
          class="template-customizer-theme-css"/>
    {{--    <link rel="stylesheet" href="storage/assets/css/demo.css" />--}}

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="storage/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css"/>
    <link rel="stylesheet" href="storage/assets/vendor/libs/typeahead-js/typeahead.css"/>
    <!-- Vendor -->
    <link rel="stylesheet" href="storage/assets/vendor/libs/@form-validation/form-validation.css"/>

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="storage/assets/vendor/css/pages/page-auth.css"/>

    <!-- Helpers -->
    <script src="storage/assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="storage/assets/vendor/js/template-customizer.js"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="storage/assets/js/config.js"></script>
</head>

<body>
<!-- Content -->

<div class="position-relative">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Reset Password') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="row mb-3">
                                <label for="email"
                                       class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email"
                                           class="form-control @error('email') is-invalid @enderror" name="email"
                                           value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password"
                                       class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                           class="form-control @error('password') is-invalid @enderror" name="password"
                                           required autocomplete="new-password">

                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password-confirm"
                                       class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control"
                                           name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Reset Password') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    <!-- / Content -->

    <!-- Core JS -->
    <!-- build:js storage/assets/vendor/js/core.js -->
    <script src="storage/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="storage/assets/vendor/libs/popper/popper.js"></script>
    <script src="storage/assets/vendor/js/bootstrap.js"></script>
    <script src="storage/assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="storage/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="storage/assets/vendor/libs/hammer/hammer.js"></script>
    <script src="storage/assets/vendor/libs/i18n/i18n.js"></script>
    <script src="storage/assets/vendor/libs/typeahead-js/typeahead.js"></script>
    <script src="storage/assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="storage/assets/vendor/libs/@form-validation/popular.js"></script>
    <script src="storage/assets/vendor/libs/@form-validation/bootstrap5.js"></script>
    <script src="storage/assets/vendor/libs/@form-validation/auto-focus.js"></script>

    <!-- Main JS -->
    <script src="storage/assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="storage/assets/js/pages-auth.js"></script>
</body>
</html>



