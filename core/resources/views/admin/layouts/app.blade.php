@extends('admin.layouts.master')

@section('content')
    <!-- page-wrapper start -->
    <div class="page-wrapper default-version">
        @include(session('crm_admin_bridge') && empty(session('crm_admin_bridge.full_access'))
            ? 'admin.partials.sidenav_crm_scoped'
            : 'admin.partials.sidenav')
        @include('admin.partials.topnav')

        <div class="body-wrapper">
            <div class="bodywrapper__inner">

                @include('admin.partials.breadcrumb')

                @yield('panel')


            </div><!-- bodywrapper__inner end -->
        </div><!-- body-wrapper end -->
    </div>



@endsection
