@php
    $adminUser = Auth::guard('admin')->user();
@endphp
<div class="col-md-3 left_col menu_fixed">
    <div class="left_col scroll-view">
        <div class="navbar nav_title" style="border: 0;">
            <a href="{{ route('admin.dashboard') }}" class="site_title"><i class="fa fa-paw"></i> <span>Gentelella
                    Alela!</span></a>
        </div>

        <div class="clearfix"></div>
        <!-- menu profile quick info -->
        <div class="profile clearfix">
            <div class="profile_pic">
                <img src="{{ $adminUser ? $adminUser->getAvatarUrl() : asset('assets/clients/img/default-avatar.png') }}"
                    alt="..." class="img-circle profile_img">
            </div>
            <div class="profile_info">
                <span>Xin chào</span>
                <h2>{{ $adminUser ? $adminUser->name : 'Admin' }}</h2>
            </div>
        </div>
        <br />
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
                <h3>Tổng quan</h3>
                <ul class="nav side-menu">
                    <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-home"></i> Trang chủ</a>
                    </li>
                    @if ($adminUser && $adminUser->role && $adminUser->role->permissions->contains('name', 'manage_users'))
                        <li><a href="{{ route('admin.users') }}"><i class="fa fa-user"></i> Quản lí người dùng</a></li>
                    @endif
                    @if ($adminUser && $adminUser->role && $adminUser->role->permissions->contains('name', 'manage_categories'))
                        <li><a href="#"><i class="fa fa-lock"></i>Quản lí danh mục<span
                                    class="fa fa-chevron-down"></span></a>
                            <ul class="nav child_menu">
                                <li><a href="{{ route('admin.categories.add') }}">Thêm danh mục</a></li>
                                <li><a href="{{ route('admin.categories.index') }}">Danh sách danh mục</a></li>
                            </ul>
                        </li>
                    @endif
                    @if ($adminUser && $adminUser->role && $adminUser->role->permissions->contains('name', 'manage_products'))
                        <li><a href="#"><i class="fa fa-desktop"></i>Quản lí sản phẩm<span
                                    class="fa fa-chevron-down"></span></a>
                            <ul class="nav child_menu">
                                <li><a href="{{ route('admin.product.add') }}">Thêm sản phẩm</a></li>
                                <li><a href="{{ route('admin.products.index') }}">Danh sách sản phẩm</a></li>
                            </ul>
                        </li>
                    @endif
                    @if ($adminUser && $adminUser->role && $adminUser->role->permissions->contains('name', 'manage_orders'))
                        <li><a href="{{ route('admin.orders.index') }}"><i class="fa fa-edit"></i> Quản lí đơn hàng</a>
                        </li>
                    @endif
                    @if ($adminUser && $adminUser->role && $adminUser->role->permissions->contains('name', 'manage_contacts'))
                        <li><a href="{{ route('admin.contacts.index') }}"><i class="fa fa-envelope"></i>Liên hệ</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="sidebar-footer hidden-small">
            <a data-toggle="tooltip" data-placement="top" title="Đăng xuất" href="{{ route('admin.logout') }}">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
            </a>
        </div>
    </div>
</div>
