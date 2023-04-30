@extends('shop::layouts.app')

@section('content')
    <cart-widget></cart-widget>

    <cart-drawer
        :checkout-url="'{{ route('checkout') }}'"
        @openCartDrawer="openDrawer"
    ></cart-drawer>

    @include('shop.partials.productList')
@endsection