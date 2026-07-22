@extends('layouts.admin')

@section('title', 'Danh mục')

@section('content')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Quản lí danh mục <small>Danh sách tât cả danh mục</small></h3>
                </div>
            </div>
            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Danh sách danh mục<small></small></h2>
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
                                                            Trang quản lí danh mục cho phép admin tạo, chỉnh sửa và xóa các
                                                            danh mục sản phẩm.
                                                            Các danh mục sản phẩm có tổ chức theo từng nhóm, giúp khách hàng
                                                            dễ dàng tìm kiếm và lựa chọn.
                                                            Dữ liệu được hiển thị dưới dang bảng, hỗ trợ tìm kiếm, sắp xếp
                                                            và thao tác nhanh.
                                                        </p>
                                                        <table id="datatable-buttons"
                                                            class="table table-striped table-bordered" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>Hình ảnh</th>
                                                                    <th>Tên danh mục</th>
                                                                    <th>Slug</th>
                                                                    <th>Mô tả</th>
                                                                    <th>Chỉnh sửa</th>
                                                                    <th>Xóa</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($categories as $category)
                                                                    <tr id="category-row-{{ $category->id }}">
                                                                        <td>
                                                                            @if ($category->image)
                                                                                <img src="{{ asset('storage/' . $category->image) }}"
                                                                                    alt="{{ $category->name }}"
                                                                                    style="width: 100px;">
                                                                            @else
                                                                                <span>Không có ảnh</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>{{ $category->name }}</td>
                                                                        <td>{{ $category->slug }}</td>
                                                                        <td>{{ $category->description }}</td>
                                                                        <td>
                                                                            <a class="btn btn-app btn-update-category"
                                                                                data-toggle="modal"
                                                                                data-target="#modalUpdate-{{ $category->id }}">
                                                                                <i class="fa fa-edit"></i>
                                                                                Chỉnh sửa
                                                                            </a>
                                                                        </td>
                                                                        <td><a class="btn btn-app btn-delete-category"
                                                                                data-id="{{ $category->id }}"><i
                                                                                    class="fa fa-trash"></i>
                                                                                Xóa
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                    <!-- Modal -->
                                                                    <div class="modal fade"
                                                                        id="modalUpdate-{{ $category->id }}" tabindex="-1"
                                                                        aria-labelledby="categoryModalLabel"
                                                                        aria-hidden="true">
                                                                        <div class="modal-dialog">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title"
                                                                                        id="categoryModalLabel">Chỉnh sửa
                                                                                    </h5>
                                                                                    <button type="button" class="btn-close"
                                                                                        data-dismiss="modal"
                                                                                        aria-label="Close">
                                                                                        <span
                                                                                            aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <form id="update-category"
                                                                                        method="POST"
                                                                                        class="form-horizontal form-label-left"
                                                                                        enctype="multipart/form-data">
                                                                                        @csrf
                                                                                        <div class="item form-group">
                                                                                            <label
                                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                                for="category-name">Tên
                                                                                                danh mục
                                                                                                <span
                                                                                                    class="required">*</span>
                                                                                            </label>
                                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                                <input type="text"
                                                                                                    id="category-name"
                                                                                                    required="required"
                                                                                                    class="form-control "
                                                                                                    value="{{ $category->name }}"
                                                                                                    name="category-name">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="item form-group">
                                                                                            <label
                                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                                for="category-description">Mô
                                                                                                tả danh mục
                                                                                                <span
                                                                                                    class="required">*</span>
                                                                                            </label>
                                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                                <input type="text"
                                                                                                    id="category-description"
                                                                                                    name="category-description"
                                                                                                    required="required"
                                                                                                    class="form-control"
                                                                                                    value="{{ $category->description }}">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="item form-group">
                                                                                            <img src=""
                                                                                                alt="">
                                                                                            <label
                                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                                for="category-image">Hình
                                                                                                ảnh</label>
                                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                                <img id="category-image-preview"
                                                                                                    src="{{ asset('storage/' . $category->image) }}"
                                                                                                    alt="{{ $category->name }}"
                                                                                                    class="image-preview">
                                                                                                <label
                                                                                                    class="custom-file-upload"
                                                                                                    for="category-image-{{ $category->id }}">Chọn
                                                                                                    ảnh</label>
                                                                                                <input type="file"
                                                                                                    id="category-image-{{ $category->id }}"
                                                                                                    name="category-image"
                                                                                                    class="category-image"
                                                                                                    data-id="{{ $category->id }}"
                                                                                                    accept="image/*">
                                                                                            </div>
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button"
                                                                                        class="btn btn-secondary"
                                                                                        data-dismiss="modal">Đóng</button>
                                                                                    <button type="button"
                                                                                        class="btn btn-primary btn-update-submit-category"
                                                                                        data-id="{{ $category->id }}">Lưu
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
