@php
$configData = Helper::appClasses();
$isFront = true;
@endphp

@section('layoutContent')

@extends('layouts/commonMaster')

@include('front.partials.navbar')

<div class="d-flex flex-column min-vh-100">
    <!-- Sections:Start -->
    @yield('content')
    <!-- / Sections:End -->

    @include('front.partials.footer')
</div>
@endsection
