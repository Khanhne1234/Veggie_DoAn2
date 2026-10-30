@extends('layouts.client')
@section('title', 'Giỏ hàng')
@section('breadcrumb', 'Giỏ hàng')

@section('content')
    @include('clients.components.include.cart_content')
    @if (false)
    <div class="liton__shoping-cart-area mb-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div id="cart-page-content" class="shoping-cart-inner" aria-live="polite">
                        @include('clients.components.include.cart_content')
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection
