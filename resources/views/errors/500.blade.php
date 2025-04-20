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
    <link rel="icon" type="image/x-icon" href="{{ getGlobalImage('Favicon') }}"/>

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
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/fonts/flag-icons.css') }}"/>

    <!-- Menu waves for no-customizer fix -->
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/libs/node-waves/node-waves.css') }}"/>

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/css/rtl/core.css') }}"
          class="template-customizer-core-css"/>
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/css/rtl/theme-default.css') }}"
          class="template-customizer-theme-css"/>
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}"/>
    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/libs/typeahead-js/typeahead.css') }}"/>

    <link rel="stylesheet" href="{{ asset('storage/assets/vendor/css/pages/page-misc.css') }}" />
    <!-- Helpers -->
    <script src="{{ asset('storage/assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    {{--    <script src="{{ asset('storage/assets/vendor/js/template-customizer.js') }}"></script>--}}
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('storage/assets/js/config.js') }}"></script>
</head>

<body>
<div class="misc-wrapper">
    <h1 class="mb-2 mx-2" style="font-size: 6rem">500</h1>
    <h4 class="mb-2">Internal server error 🔐</h4>
    <p class="mb-2 mx-2">Oops somthing went wrong.</p>
    <div class="d-flex justify-content-center mt-5">
        <div class="d-flex flex-column align-items-center">
            <img
                src="{{ getGlobalImage('Library') }}"
                alt="misc-under-maintenance"
                class="img-fluid z-1"
                width="290"/>
            <div>
                <a href="{{ route('home') }}" class="btn btn-primary text-center my-5">Back to home</a>
            </div>
        </div>
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

<!-- Main JS -->
<script src="{{ asset('storage/assets/js/main.js') }}"></script>
</body>
</html>
