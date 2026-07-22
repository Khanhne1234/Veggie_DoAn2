@extends('layouts.client')
@section('title', 'Sản phẩm')
@section('breadcrumb', 'Sản phẩm')

@section('content')
    <!-- PRODUCT DETAILS AREA START -->
    <div class="ltn__product-area ltn__product-gutter">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 order-lg-2 mb-120">
                    <div class="ltn__shop-options">
                        <ul>
                            <li>
                                <div class="ltn__grid-list-tab-menu ">
                                    <div class="nav">
                                        <a class="active show" data-bs-toggle="tab" href="#liton_product_grid"><i
                                                class="fas fa-th-large"></i></a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="short-by text-center">
                                    <select id="sort-by" class="nice-select">
                                        <option value="default">Sắp xếp mặc định</option>
                                        <option value="new_arrivals">Sắp xếp theo sản phẩm mới</option>
                                        <option value="price_low_high">Sắp xếp theo giá: thấp đến cao</option>
                                        <option value="price_high_low">Sắp xếp theo giá: cao đến thấp</option>
                                    </select>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content">
                        <div id="loading-spinner" style="display: none;">
                            <span class="loader"></span>
                        </div>
                        <div class="tab-pane fade active show" id="liton_product_grid">
                            @include('clients.components.products_grid', ['products' => $products])
                        </div>
                    </div>
                    <div class="ltn__pagination-area text-center">
                        <div class="ltn__pagination">
                            {!! $products->links('clients.components.pagination.pagination_custom') !!}
                        </div>
                    </div>
                </div>
                <div class="col-lg-4  mb-120">
                    <aside class="sidebar ltn__shop-sidebar">
                        <!-- Category Widget -->
                        <div class="widget ltn__menu-widget">
                            <h4 class="ltn__widget-title ltn__widget-title-border">Danh mục sản phẩm</h4>
                            <ul>
                                @foreach ($categories as $category)
                                    <li>
                                        <a href="javascript:void(0)" class="category-filter {{ request('category_id') == $category->id ? 'active' : '' }}"
                                            data-id="{{ $category->id }}">{{ $category->name }} <span><i
                                                    class="fas fa-long-arrow-alt-right"></i></span></a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <!-- Price Filter Widget -->
                        <div class="widget ltn__price-filter-widget">
                            <h4 class="ltn__widget-title ltn__widget-title-border">Bộ lọc giá</h4>
                            <div class="price_filter">
                                <div class="price_slider_amount">
                                    <span class="price-label">Giá:</span>
                                    <input type="text" class="amount" name="price" placeholder="Thêm giá của bạn"
                                        readonly />
                                </div>
                                <div class="slider-range"></div>
                            </div>
                        </div>
                        <!-- Top Rated Product Widget -->
                        <div class="widget ltn__top-rated-product-widget">
                            <h4 class="ltn__widget-title ltn__widget-title-border">Sản phẩm nổi bật</h4>
                            <ul>
                                @foreach($productsHightRate as $topProduct)
                                    <li>
                                        <div class="top-rated-product-item clearfix">
                                            <div class="top-rated-product-img">
                                                <a href="{{ route('product.detail', $topProduct->slug) }}">
                                                    <img src="{{ $topProduct->image_url ?? asset('storage/uploads/products/default_product.webp') }}"
                                                        alt="{{ $topProduct->name }}">
                                                </a>
                                            </div>
                                            <div class="top-rated-product-info">
                                                <div class="product-ratting">
                                                    @include('clients.components.include.rating', [
                                                        'product' => $topProduct,
                                                    ])
                                                </div>
                                                <h6><a href="{{ route('product.detail', $topProduct->slug) }}">{{ $topProduct->name }}</a></h6>
                                                <div class="product-price">
                                                    <span>{{ number_format($topProduct->price, 0, ',', '.') }}VND</span>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <!-- Search Widget -->
                        <div class="widget ltn__search-widget">
                            <h4 class="ltn__widget-title ltn__widget-title-border">Tìm kiếm</h4>
                            <form method="GET" action="{{ route('search') }}">
                                <input type="text" name="keyword" placeholder="Nhập tên sản phẩm ...">
                                <button type="submit"><i class="fas fa-search"></i></button>
                            </form>
                        </div>
                        <!-- Banner Widget -->
                        <div class="widget ltn__banner-widget">
                            <a href="{{ route('products.index') }}"><img
                                    src="{{ asset('assets/clients/img/banner/banner-1.jpg') }}" alt="#"></a>
                        </div>

                    </aside>
                </div>
            </div>
        </div>
    </div>
    <!-- PRODUCT DETAILS AREA END -->

@endsection
