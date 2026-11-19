@extends('layouts.admin')

@section('title', 'Quản lý người dùng')

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Quản lý người dùng</h3>
                </div>
            </div>
            <div class="clearfix"></div>

            {{-- Form tìm kiếm --}}
            <div class="x_panel">
                <div class="x_content">
                    <form method="GET" action="{{ route('admin.users') }}" id="searchForm">
                        <div class="row" style="display: flex; align-items: flex-end; gap: 10px; flex-wrap: wrap;">
                            {{-- Từ khóa --}}
                            <div class="col-md-3 col-sm-6">
                                <label for="keyword">Tìm kiếm</label>
                                <input type="text" name="keyword" id="keyword" class="form-control"
                                    placeholder="Tên, email, SĐT..." value="{{ request('keyword') }}">
                            </div>

                            {{-- Lọc theo vai trò --}}
                            <div class="col-md-2 col-sm-6">
                                <label for="role_id">Vai trò</label>
                                <select name="role_id" id="role_id" class="form-control">
                                    <option value="">-- Tất cả --</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Lọc theo trạng thái --}}
                            <div class="col-md-2 col-sm-6">
                                <label for="status">Trạng thái</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">-- Tất cả --</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động
                                    </option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt
                                    </option>
                                    <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Bị chặn
                                    </option>
                                    <option value="deleted" {{ request('status') == 'deleted' ? 'selected' : '' }}>Đã xóa
                                    </option>
                                </select>
                            </div>

                            {{-- Nút tìm kiếm & xóa lọc --}}
                            <div class="col-md-3 col-sm-6">
                                <button type="submit" class="btn btn-success btn-sm" style="margin-right: 5px;">
                                    <i class="fa fa-search"></i> Tìm kiếm
                                </button>
                                <a href="{{ route('admin.users') }}" class="btn btn-default btn-sm">
                                    <i class="fa fa-times"></i> Xóa lọc
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Kết quả --}}
            <div class="x_panel">
                <div class="x_content">
                    <div class="clearfix"></div>

                    {{-- Thông tin kết quả --}}
                    <div style="margin-bottom: 15px;">
                        <span class="text-muted">
                            Tìm thấy <strong>{{ $users->total() }}</strong> người dùng
                            @if (request('keyword') || request('role_id') || request('status'))
                                (đã lọc)
                            @endif
                        </span>
                    </div>

                    @if ($users->count() > 0)
                        <div class="row">
                            @foreach ($users as $user)
                                <div class="col-md-4 col-sm-4  profile_details">
                                    <div class="well profile_view">
                                        <div class="col-sm-12">
                                            <h4 class="brief text-uppercase"><i>{{ $user->role->name }}</i></h4>
                                            <div class="left col-md-7 col-sm-7">
                                                <h2>{{ $user->name }}</h2>
                                                <p><strong>Email: </strong> {{ $user->email }}</p>
                                                <ul class="list-unstyled">
                                                    <li><i class="fa fa-building"></i> Address: {{ $user->address }}</li>
                                                    <li><i class="fa fa-phone"></i> Phone: {{ $user->phone_number }}</li>
                                                </ul>
                                            </div>
                                            <div class="right col-md-5 col-sm-5 text-center">
                                                <img src="{{ $user->getAvatarUrl() }}" alt=""
                                                    class="img-circle img-fluid">
                                            </div>
                                        </div>
                                        <div class=" profile-bottom text-center">
                                            <div class="col-sm-4 emphasis">
                                            </div>
                                            <div class=" col-sm-10 emphasis">
                                                @if (in_array($user->role->name, ['customer', 'staff'], true))
                                                    @if ($user->status !== 'deleted')
                                                        <button type="button" class="btn btn-primary btn-sm changeUserRole"
                                                            data-userid="{{ $user->id }}"
                                                            data-role="{{ $user->role->name === 'customer' ? 'staff' : 'customer' }}"
                                                            data-url="{{ route('admin.user.updateRole') }}">
                                                            <i class="fa fa-user"></i>
                                                            {{ $user->role->name === 'customer' ? 'Nâng lên nhân viên' : 'Hạ xuống khách hàng' }}
                                                        </button>
                                                        @if ($user->status === 'banned')
                                                            <button type="button" class="btn btn-success btn-sm changeStatus"
                                                                data-userid="{{ $user->id }}" data-status="active">
                                                                <i class="fa fa-unlock"></i> Bỏ chặn
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-warning btn-sm changeStatus"
                                                                data-userid="{{ $user->id }}" data-status="banned">
                                                                <i class="fa fa-ban"></i> Chặn
                                                            </button>
                                                        @endif
                                                        <button type="button" class="btn btn-danger btn-sm changeStatus"
                                                            data-userid="{{ $user->id }}" data-status="deleted">
                                                            <i class="fa fa-trash"></i> Xóa
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-success btn-sm changeStatus"
                                                            data-userid="{{ $user->id }}" data-status="active">
                                                            <i class="fa fa-undo"></i> Khôi phục
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Phân trang --}}
                        <div class="text-center">
                            {{ $users->links() }}
                        </div>
                    @else
                        <div class="text-center" style="padding: 40px 0;">
                            <i class="fa fa-search" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                            <h4 style="color: #999;">Không tìm thấy người dùng nào</h4>
                            <p class="text-muted">Thử thay đổi từ khóa hoặc bộ lọc để tìm kiếm lại.</p>
                            <a href="{{ route('admin.users') }}" class="btn btn-default btn-sm"
                                style="margin-top: 10px;">
                                <i class="fa fa-refresh"></i> Xóa bộ lọc
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
