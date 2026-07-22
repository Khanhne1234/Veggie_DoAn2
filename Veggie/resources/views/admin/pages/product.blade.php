@extends('layouts.admin')

@section('title', 'Danh sách sản phẩm')

@section('content')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Quản lí sản phẩm <small>Danh sách tât cả sản phẩm</small></h3>
                </div>
            </div>
            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Danh sách sản phẩm<small></small></h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="row">

                                <div class="col-md-12 col-sm-12 ">
                                    <div class="x_panel">
                                        <div class="x_content">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="card-box table-responsive">
                                                        <p class="text-muted font-13 m-b-30">
                                                            Trang quản lí sản phẩm cho phép admin tạo, chỉnh sửa và xóa các
                                                            sản phẩm.
                                                            Các sản phẩm có tổ chức theo từng nhóm, giúp khách hàng
                                                            dễ dàng tìm kiếm và lựa chọn.
                                                            Dữ liệu được hiển thị dưới dang bảng, hỗ trợ tìm kiếm, sắp xếp
                                                            và thao tác nhanh.
                                                        </p>
                                                        <table id="datatable-buttons"
                                                            class="table table-striped table-bordered" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>Hình ảnh</th>
                                                                    <th>Tên sản phẩm</th>
                                                                    <th>Danh mục</th>
                                                                    <th>Slug</th>
                                                                    <th>Mô tả</th>
                                                                    <th>Số lượng</th>
                                                                    <th>Giá</th>
                                                                    <th>Đơn vị</th>
                                                                    <th>Trạng thái</th>
                                                                    <th>Chỉnh sửa</th>
                                                                    <th>Xóa</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($products as $product)
                                                                    <tr id="product-row-{{ $product->id }}">
                                                                        <td>
                                                                            @if ($product->image_url)
                                                                                <img src="{{ $product->image_url }}"
                                                                                    alt="{{ $product->name }}"
                                                                                    class="image-product"
                                                                                    style="width: 100px;">
                                                                            @else
                                                                                <span>Không có ảnh</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>{{ $product->name }}</td>
                                                                        <td>{{ $product->category->name }}</td>
                                                                        <td>{{ $product->slug }}</td>
                                                                        <td>{{ $product->description }}</td>
                                                                        <td>{{ $product->stock }}</td>
                                                                        <td>{{ number_format($product->price, 0, ',', '.') }}VNĐ
                                                                        </td>
                                                                        <td>{{ $product->unit }}</td>
                                                                        <td>{{ $product->status == 'in_stock' ? 'Còn hàng' : 'Hết hàng' }}
                                                                        </td>
                                                                        <td>
                                                                            <a class="btn btn-app btn-update-product"
                                                                                data-toggle="modal"
                                                                                data-target="#modalUpdate-{{ $product->id }}">
                                                                                <i class="fa fa-edit"></i>
                                                                                Chỉnh sửa
                                                                            </a>
                                                                        </td>
                                                                        <td><a class="btn btn-app btn-delete-product"
                                                                                data-id="{{ $product->id }}"><i
                                                                                    class="fa fa-trash"></i>
                                                                                Xóa
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                    <!-- Modal -->
                                                                    <div class="modal fade"
                                                                        id="modalUpdate-{{ $product->id }}" tabindex="-1"
                                                                        aria-labelledby="productModalLabel"
                                                                        aria-hidden="true">
                                                                        <div class="modal-dialog">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title"
                                                                                        id="productModalLabel">Chỉnh sửa
                                                                                    </h5>
                                                                                    <button type="button" class="btn-close"
                                                                                        data-dismiss="modal"
                                                                                        aria-label="Close">
                                                                                        <span
                                                                                            aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <form id="update-product" method="POST"
                                                                                        class="form-horizontal form-label-left"
                                                                                        enctype="multipart/form-data">
                                                                                        @csrf
                                                                                        <div class="item form-group">
                                                                                            <label
                                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                                for="product-name">Tên
                                                                                                sản phẩm
                                                                                                <span
                                                                                                    class="required">*</span>
                                                                                            </label>
                                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                                <input type="text"
                                                                                                    id="product-name"
                                                                                                    required
                                                                                                    class="form-control "
                                                                                                    value="{{ $product->name }}"
                                                                                                    name="name">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="item form-group">
                                                                                            <label
                                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                                for="product-name">Danh mục
                                                                                                <span
                                                                                                    class="required">*</span>
                                                                                            </label>
                                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                                <select name="category_id"
                                                                                                    id="category_id"
                                                                                                    class="form-control">
                                                                                                    @foreach ($categories as $category)
                                                                                                        <option
                                                                                                            value="{{ $category->id }}"
                                                                                                            {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                                                                            {{ $category->name }}
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="item form-group">
                                                                                            <label
                                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                                for="product-description">Mô
                                                                                                tả sản phẩm
                                                                                                <span
                                                                                                    class="required">*</span>
                                                                                            </label>
                                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                                <input type="text"
                                                                                                    id="product-description"
                                                                                                    name="description"
                                                                                                    required
                                                                                                    class="form-control"
                                                                                                    value="{{ $product->description }}">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="item form-group">
                                                                                            <label
                                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                                for="product-price">Giá
                                                                                                <span
                                                                                                    class="required">*</span>
                                                                                            </label>
                                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                                <input type="text"
                                                                                                    id="product-price"
                                                                                                    name="price"
                                                                                                    required
                                                                                                    class="form-control"
                                                                                                    value="{{ $product->price }}">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="item form-group">
                                                                                            <label
                                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                                for="product-stock">Số
                                                                                                lượng
                                                                                                <span
                                                                                                    class="required">*</span>
                                                                                            </label>
                                                                                            <div
                                                                                                class="col-md-6 col-sm-6 ">
                                                                                                <input type="text"
                                                                                                    id="product-stock"
                                                                                                    name="stock"
                                                                                                    required
                                                                                                    class="form-control"
                                                                                                    value="{{ $product->stock }}">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="item form-group">
                                                                                            <label
                                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                                for="product-unit">Đơn vị
                                                                                                <span
                                                                                                    class="required">*</span>
                                                                                            </label>
                                                                                            <div
                                                                                                class="col-md-6 col-sm-6 ">
                                                                                                <input type="text"
                                                                                                    id="product-unit"
                                                                                                    name="unit"
                                                                                                    required
                                                                                                    class="form-control"
                                                                                                    value="{{ $product->unit }}">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="item form-group">
                                                                                            <img src=""
                                                                                                alt="">
                                                                                            <label
                                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                                for="product-image">Hình
                                                                                                ảnh</label>
                                                                                            <div
                                                                                                class="col-md-6 col-sm-6 ">
                                                                                                <label
                                                                                                    class="custom-file-upload"
                                                                                                    for="product-image-{{ $product->id }}">Chọn
                                                                                                    ảnh</label>
                                                                                                <input type="file"
                                                                                                    id="product-image-{{ $product->id }}"
                                                                                                    name="images[]"
                                                                                                    class="product-image"
                                                                                                    data-id="{{ $product->id }}"
                                                                                                    accept="image/*"
                                                                                                    multiple>
                                                                                                <div class="image-preview-container-{{ $product->id }}"
                                                                                                    class="imgae-preview-container image-preview-listproduct"
                                                                                                    data-id="{{ $product->id }}">
                                                                                                    @foreach ($product->images as $key => $image)
                                                                                                        <img src="{{ asset('storage/' . $image->image) }}"
                                                                                                            alt="Ảnh sản phẩm"
                                                                                                            style="width: 100px; height: 100px;">
                                                                                                    @endforeach
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button"
                                                                                        class="btn btn-secondary"
                                                                                        data-dismiss="modal">Đóng</button>
                                                                                    <button type="button"
                                                                                        class="btn btn-primary btn-update-submit-product"
                                                                                        data-id="{{ $product->id }}">Lưu
                                                                                        thay
                                                                                        đổi</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
