@extends('layouts.admin')

@section('title', 'Quản lí thông tin tài khoản')

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="row">
                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Tài khoản</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="col-md-3 col-sm-3  profile_left">
                                <div class="profile_img">
                                    <div id="crop-avatar">
                                        <!-- Current avatar -->
                                        <img id="avatar-preview" class="img-responsive avatar-view"
                                            src="{{ $user->getAvatarUrl() }}" alt="Avatar"
                                            title="Avatar tài khoản">
                                    </div>
                                    <form enctype="multipart/form-data">
                                        <input type="file" name="avatar" id="avatar" accept="/*"
                                            style="display: none;">
                                        <a href="javascript:void(0);" class="btn btn-success update-avatar"
                                            style="margin-top: 10px;">
                                            <i class="fa fa-edit"> </i> Đổi ảnh đại diện
                                        </a>
                                    </form>
                                </div>
                                <h3>{{ $user->name }}</h3>

                                <ul class="list-unstyled user_data">
                                    <li><i class="fa fa-map-marker user-profile-icon"></i><span
                                            id="user-address">{{ $user->address }}</span>
                                    </li>

                                    <li>
                                        <i class="fa fa-briefcase user-profile-icon"></i> <span
                                            id="user-email">{{ $user->email }}</span>
                                    </li>

                                    <li class="m-top-xs">
                                        <i class="fa fa-phone user-profile-icon"></i>
                                        <span id="user-phone">{{ $user->phone_number }}</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-9 col-sm-9 ">
                                <form id="update-profile" data-parsley-validate class="form-horizontal form-label-left">
                                    <div class="item form-group">
                                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="name">Họ và tên
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 ">
                                            <input type="text" id="name" required class="form-control "
                                                value="{{ $user->name ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="email">Email
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 ">
                                            <input type="text" id="email" name="email" required readonly
                                                class="form-control" value="{{ $user->email ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="phone">Số điện
                                            thoại <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 ">
                                            <input type="text" id="phone" name="phone" required
                                                class="form-control" value="{{ $user->phone_number ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="address">Địa
                                            chỉ <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 ">
                                            <input type="text" id="address" name="address" required
                                                class="form-control" value="{{ $user->address ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="ln_solid"></div>
                                    <div class="item form-group">
                                        <div class="col-md-6 col-sm-6 offset-md-3">
                                            <button class="btn btn-primary" type="reset">Reset</button>
                                            <button type="submit" class="btn btn-success">Cập nhật</button>
                                            <button type="submit" class="btn btn-warning form-change-password">Đổi mật
                                                khẩu</button>
                                        </div>
                                    </div>
                                </form>
                                <!--Thay đổi mật khẩu-->
                                <form id="change-password" data-parsley-validate class="form-horizontal form-label-left"
                                    style="display: none;">
                                    <div class="ln_solid"></div>
                                    <div class="item form-group">
                                        <label class="col-form-label col-md-3 col-sm-3 label-align">Mật
                                            khẩu cũ
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 ">
                                            <input type="password" id="old-password" required class="form-control ">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="new-password">Mật
                                            khẩu mới
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 ">
                                            <input type="password" id="new-password" name="new-password" required
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="col-form-label col-md-3 col-sm-3 label-align"
                                            for="confirm-password">Xác nhận mật khẩu mới
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 ">
                                            <input type="password" id="confirm-password" name="confirm-password" required
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <div class="col-md-6 col-sm-6 offset-md-3">
                                            <button class="btn btn-primary" type="reset">Reset</button>
                                            <button type="submit" class="btn btn-success">Cập nhật</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
