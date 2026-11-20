@extends('kaheel.layouts.forma')
@php $title = 'الباقات | FORMA INTERIORS'; $nav = 'packages'; @endphp
@section('content')
    @include('kaheel.pages.furniture')
    @include('kaheel.partials.catalog-packages')
    @include('kaheel.partials.visit-cta')
@endsection
