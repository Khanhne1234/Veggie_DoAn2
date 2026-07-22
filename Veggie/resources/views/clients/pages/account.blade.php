@extends('layouts.client')
@section('title', 'Tài khoản')
@section('breadcrumb', 'Tài khoản')
@section('content')
    <!-- WISHLIST AREA START -->
    <div class="liton__wishlist-area pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- PRODUCT TAB AREA START -->
                    <div class="ltn__product-tab-area">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="ltn__tab-menu-list mb-50">
                                        <div class="nav">
                                            <a class="active show" data-bs-toggle="tab" href="#liton_tab_dashboard">Bảng
                                                điều khiển <i class="fas fa-home"></i></a>
                                            <a data-bs-toggle="tab" href="#liton_tab_orders">Đơn hàng <i
                                                    class="fas fa-file-alt"></i></a>
                                            <a data-bs-toggle="tab" href="#liton_tab_addresses">Địa chỉ <i
                                                    class="fas fa-map-marker-alt"></i></a>
                                            <a data-bs-toggle="tab" href="#liton_tab_account">Thông tin tài khoản <i
                                                    class="fas fa-user"></i></a>
                                            <a data-bs-toggle="tab" href="#liton_tab_password">Đổi mật khẩu <i
                                                    class="fas fa-lock"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="tab-content">
                                        <div class="tab-pane fade active show" id="liton_tab_dashboard">
                                            <div class="ltn__myaccount-tab-content-inner">
                                                <p>Xin chào <strong>{{ $user->name }}</strong> (không phải là
                                                    <strong>{{ $user->email }}</strong>?
                                                    <small><a href="{{ url('logout') }}">Đăng xuất</a></small> )
                                                </p>
                                                <p>Từ bảng điều khiển tài khoản, bạn có thể xem <span>các đơn hàng gần
                                                        đây</span>,
                                                    quản lý <span>địa chỉ giao hàng và thanh toán</span>, và <span>chỉnh sửa
                                                        mật khẩu
                                                        cùng thông tin tài khoản</span>.</p>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="liton_tab_orders">
                                            <div class="ltn__myaccount-tab-content-inner">
                                                <div class="table-responsive"
                                                    style="overflow-x: auto; overflow-y: scroll; max-height: 400px;">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>Đơn hàng</th>
                                                                <th>Ngày</th>
                                                                <th>Trạng thái</th>
                                                                <th>Tổng tiền</th>
                                                                <th>Thao tác</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($orders as $order)
                                                                <tr>
                                                                    <td>#{{ $order->id }} </td>
                                                                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                                                    <td>
                                                                        @if ($order->status == 'pending')
                                                                            <span class="badge bg-warning">Đang chờ xử
                                                                                lý</span>
                                                                        @elseif ($order->status == 'processing')
                                                                            <span class="badge bg-info">Đang xử lý</span>
                                                                        @elseif ($order->status == 'completed')
                                                                            <span class="badge bg-success">Hoàn thành</span>
                                                                        @elseif ($order->status == 'cancelled')
                                                                            <span class="badge bg-danger">Đã hủy</span>
                                                                        @else
                                                                            <span
                                                                                class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ number_format($order->total_price, 0, ',', '.') }}VNĐ
                                                                    </td>
                                                                    <td><a href="{{ route('orders.show', $order->id) }}"
                                                                            class="btn btn-sm btn-primary">Xem chi tiết</a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="liton_tab_addresses">
                                            <div class="ltn__myaccount-tab-content-inner">
                                                <p>Các địa chỉ sau sẽ được sử dụng mặc định trên trang thanh toán.</p>
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>Tên người nhận</th>
                                                                <th>Địa chỉ</th>
                                                                <th>Thành phố</th>
                                                                <th>Số điện thoại</th>
                                                                <th>Mặc định</th>
                                                                <th>Hành động</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($addresses as $address)
                                                                <tr>
                                                                    <td>{{ $address->full_name }}</td>
                                                                    <td>{{ $address->address }}</td>
                                                                    <td>{{ $address->city }}</td>
                                                                    <td>{{ $address->phone }}</td>
                                                                    <td>
                                                                        @if ($address->default)
                                                                            <span class="badge bg-success">Mặc định</span>
                                                                        @else
                                                                            <form
                                                                                action="{{ route('account.address.update', ['id' => $address->id]) }}"
                                                                                method="POST" class="d-inline">
                                                                                @csrf
                                                                                @method('PUT')
                                                                                <button type="submit"
                                                                                    class="btn btn-sm btn-primary">Đặt làm
                                                                                    mặc định</button>
                                                                            </form>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <form
                                                                            action="{{ route('account.address.delete', ['id' => $address->id]) }}"
                                                                            method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit"
                                                                                class="btn btn-sm btn-danger"
                                                                                onclick="return confirm('Bạn có chắc chắn muốn xóa địa chỉ này không?')">Xóa</button>
                                                                        </form>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <button type="button" class="btn theme-btn-1 btn-effect-1 mt-3"
                                                    data-bs-toggle="modal" data-bs-target="#addAddressModal">Thêm địa
                                                    chỉ mới</button>
                                            </div>
                                        </div>

                                        <!-- Tab Account Info -->
                                        <div class="tab-pane fade" id="liton_tab_account">
                                            <div class="ltn__myaccount-tab-content-inner">
                                                <p>Các thông tin sau sẽ được sử dụng mặc định trên trang thanh toán.</p>
                                                <div class="ltn__form-box">
                                                    <form action="{{ route('account.update') }}" method="POST"
                                                        id="update-account" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="row mb-50">
                                                            <div class="col-md-12 text-center">
                                                                <div class="profile-pic-container">
                                                                    <img src="{{ $user->getAvatarUrl() }}" alt="Avatar"
                                                                        class="profile-pic" id="preview-image">
                                                                    <input type="file" name="ltn_avatar" id="avatar"
                                                                        accept="image/*" class="d-none">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="ltn_name">Họ và tên:</label>
                                                                <input type="text" name="ltn_name" id="ltn_name"
                                                                    value="{{ $user->name }}">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="ltn_phone">Số điện thoại:</label>
                                                                <input type="text" name="ltn_phone" id="ltn_phone"
                                                                    value="{{ $user->phone_number ?? '' }}"
                                                                    placeholder="Số điện thoại">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="ltn_email">Email (Không được thay
                                                                    đổi):</label>
                                                                <input type="email" name="ltn_email" id="ltn_email"
                                                                    value="{{ $user->email }}" placeholder="Email"
                                                                    readonly>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="ltn_address">Địa chỉ:</label>
                                                                <input type="text" name="ltn_address" id="ltn_address"
                                                                    value="{{ $user->address ?? '' }}"
                                                                    placeholder="Địa chỉ">
                                                            </div>
                                                        </div>
                                                        <div class="btn-wrapper">
                                                            <button type="submit"
                                                                class="btn theme-btn-1 btn-effect-1 text-uppercase">Lưu
                                                                thay
                                                                đổi</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tab Change Password -->
                                        <div class="tab-pane fade" id="liton_tab_password">
                                            <div class="ltn__myaccount-tab-content-inner">
                                                <p>Vui lòng nhập đầy đủ thông tin bên dưới để đổi mật khẩu.</p>
                                                <div class="ltn__form-box">
                                                    <form action="{{ route('account.post-change-password') }}"
                                                        method="POST" id="change-password-form">
                                                        @csrf
                                                        <fieldset>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label for="current_password">Mật khẩu hiện
                                                                        tại:</label>
                                                                    <input type="password" name="current_password"
                                                                        id="current_password"
                                                                        placeholder="Mật khẩu hiện tại" required>

                                                                    <label for="new_password">Mật khẩu mới:</label>
                                                                    <input type="password" name="new_password"
                                                                        id="new_password" placeholder="Mật khẩu mới"
                                                                        required>

                                                                    <label for="new_password_confirmation">Xác nhận mật
                                                                        khẩu
                                                                        mới:</label>
                                                                    <input type="password"
                                                                        name="new_password_confirmation"
                                                                        id="new_password_confirmation"
                                                                        placeholder="Xác nhận mật khẩu mới" required>
                                                                </div>
                                                            </div>
                                                        </fieldset>

                                                        <div class="btn-wrapper">
                                                            <button type="submit"
                                                                class="btn theme-btn-1 btn-effect-1 text-uppercase">Đổi
                                                                mật
                                                                khẩu</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal -->
                                        <div class="modal fade" id="addAddressModal" tabindex="-1"
                                            aria-labelledby="addAddressModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="addAddressModalLabel">Thêm địa chỉ mới
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('account.address.add') }}" method="POST"
                                                            id="addAddressForm">
                                                            @csrf
                                                            <div class="mb-3">
                                                                <label for="full_name" class="form-label">Tên người
                                                                    dùng:</label>
                                                                <input type="text" class="form-control" id="full_name"
                                                                    name="full_name" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="address" class="form-label">Địa chỉ:</label>
                                                                <input type="text" class="form-control" id="address"
                                                                    name="address" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="city" class="form-label">Thành
                                                                    phố:</label>
                                                                <input type="text" class="form-control" id="city"
                                                                    name="city" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="phone" class="form-label">Số điện
                                                                    thoại:</label>
                                                                <input type="text" class="form-control" id="phone"
                                                                    name="phone" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="default" class="form-label">Địa chỉ mặc
                                                                    định:</label>
                                                                <input type="hidden" name="default" value="0">
                                                                <input type="checkbox" class="form-check-input"
                                                                    id="default" name="default" value="1">
                                                            </div>
                                                            <button type="submit"
                                                                class="btn theme-btn-1 btn-effect-1">Lưu thông tin</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- PRODUCT TAB AREA END -->
                    </div>
                </div>
            </div>
        </div>
        <!-- WISHLIST AREA END -->
    @endsection
