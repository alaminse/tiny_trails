<!doctype html>
<html lang="en">
<!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> @yield('title') </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    @include('backend.includes.css')
    @yield('css')
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Header-->
        @include('backend.includes.topbar')
        <!--end::Header-->
        <!--begin::Sidebar-->
        @include('backend.includes.sidebar')

        <!--end::Sidebar-->
        <!--begin::App Main-->
        <main class="app-main">
            @yield('content')
        </main>
        <!--end::App Main-->
        <!--begin::Footer-->
        @include('backend.includes.footer')
        <!--end::Footer-->
    </div>
    @include('backend.includes.js')

    @stack('scripts')
</body>
<!--end::Body-->

</html>
