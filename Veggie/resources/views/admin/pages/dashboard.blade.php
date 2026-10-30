@extends('layouts.admin')

@section('title', 'Tổng quan')

@push('styles')
    <link href="{{ asset('assets/admin/css/dashboard.css') }}" rel="stylesheet">
@endpush

@section('content')
    @php
        $categoryColors = ['#16a34a', '#3b82f6', '#f59e0b', '#8b5cf6', '#ef4444'];
        $hasRevenueData = collect($revenueSeries['values'])->sum() > 0;
        $hasCategoryData = $categories->sum('products_count') > 0;
    @endphp

    <div class="right_col dashboard-page" role="main">
        <div class="dashboard-shell">
            <header class="dashboard-heading">
                <div>
                    <span class="dashboard-eyebrow">Bảng điều khiển</span>
                    <h1>Tổng quan cửa hàng</h1>
                    <p>Theo dõi nhanh tình hình kinh doanh và các hoạt động mới nhất.</p>
                </div>
                <div class="dashboard-date" aria-label="Ngày hiện tại">
                    <i class="fa fa-calendar-o" aria-hidden="true"></i>
                    <span>{{ now()->format('d/m/Y') }}</span>
                </div>
            </header>

            <section class="dashboard-stats" aria-label="Chỉ số tổng quan">
                <article class="stat-card stat-card--blue">
                    <div class="stat-card__icon" aria-hidden="true"><i class="fa fa-users"></i></div>
                    <div class="stat-card__content">
                        <span class="stat-card__label">Người dùng</span>
                        <strong class="stat-card__value">{{ number_format($totalUsers) }}</strong>
                        <span class="stat-card__hint">Tổng số khách hàng</span>
                    </div>
                </article>

                <article class="stat-card stat-card--green">
                    <div class="stat-card__icon" aria-hidden="true"><i class="fa fa-cubes"></i></div>
                    <div class="stat-card__content">
                        <span class="stat-card__label">Sản phẩm</span>
                        <strong class="stat-card__value">{{ number_format($totalProducts) }}</strong>
                        <span class="stat-card__hint">Sản phẩm đang còn hàng</span>
                    </div>
                </article>

                <article class="stat-card stat-card--orange">
                    <div class="stat-card__icon" aria-hidden="true"><i class="fa fa-shopping-bag"></i></div>
                    <div class="stat-card__content">
                        <span class="stat-card__label">Đơn hàng</span>
                        <strong class="stat-card__value">{{ number_format($totalOrders) }}</strong>
                        <span class="stat-card__hint">Tổng số đơn đã ghi nhận</span>
                    </div>
                </article>

                <article class="stat-card stat-card--purple">
                    <div class="stat-card__icon" aria-hidden="true"><i class="fa fa-line-chart"></i></div>
                    <div class="stat-card__content">
                        <span class="stat-card__label">Doanh thu</span>
                        <strong class="stat-card__value stat-card__value--currency">
                            {{ number_format($totalRevenue, 0, ',', '.') }} <small>VNĐ</small>
                        </strong>
                        <span class="stat-card__hint">Tổng doanh thu cửa hàng</span>
                    </div>
                </article>
            </section>

            <section class="dashboard-grid dashboard-grid--charts">
                <article class="dashboard-panel dashboard-panel--revenue">
                    <div class="dashboard-panel__header">
                        <div>
                            <span class="dashboard-panel__eyebrow">Thống kê</span>
                            <h2>Doanh thu {{ $revenuePeriodLabel }}</h2>
                        </div>
                        <form method="GET" action="{{ route('admin.dashboard') }}" class="revenue-period-form">
                            <label class="sr-only" for="revenuePeriod">Thống kê doanh thu</label>
                            <select id="revenuePeriod" name="revenue_period" class="revenue-period-select"
                                onchange="this.form.submit()">
                                <option value="day" @selected($revenuePeriod === 'day')>Theo ngày</option>
                                <option value="week" @selected($revenuePeriod === 'week')>Theo tuần</option>
                                <option value="month" @selected($revenuePeriod === 'month')>Theo tháng</option>
                            </select>
                        </form>
                    </div>

                    <div class="dashboard-panel__body dashboard-chart-wrap">
                        @if ($hasRevenueData)
                            <canvas id="dashboardRevenueChart"
                                data-labels='@json($revenueSeries['labels'])'
                                data-values='@json($revenueSeries['values'])'></canvas>
                        @else
                            <div class="dashboard-empty dashboard-empty--chart">
                                <span class="dashboard-empty__icon"><i class="fa fa-bar-chart"></i></span>
                                <strong>Chưa có dữ liệu doanh thu</strong>
                                <p>Chưa có đơn hàng trong khoảng thời gian này.</p>
                            </div>
                        @endif
                    </div>
                </article>

                <article class="dashboard-panel dashboard-panel--categories">
                    <div class="dashboard-panel__header">
                        <div>
                            <span class="dashboard-panel__eyebrow">Phân bổ</span>
                            <h2>Danh mục nổi bật</h2>
                        </div>
                        <span class="dashboard-panel__icon dashboard-panel__icon--blue" aria-hidden="true">
                            <i class="fa fa-pie-chart"></i>
                        </span>
                    </div>

                    <div class="dashboard-panel__body category-overview">
                        @if ($hasCategoryData)
                            <div class="category-chart">
                                <canvas id="dashboardCategoryChart"
                                    data-labels='@json($categories->pluck('name')->toArray())'
                                    data-counts='@json($categories->pluck('products_count')->toArray())'></canvas>
                                <div class="category-chart__center">
                                    <strong>{{ number_format($categories->sum('products_count')) }}</strong>
                                    <span>Sản phẩm</span>
                                </div>
                            </div>

                            <div class="category-list">
                                @foreach ($categories as $index => $category)
                                    <div class="category-list__item">
                                        <span class="category-list__dot"
                                            style="background-color: {{ $categoryColors[$index % count($categoryColors)] }}"></span>
                                        <span class="category-list__name" title="{{ $category->name }}">{{ $category->name }}</span>
                                        <strong>{{ number_format($category->products_count) }}</strong>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="dashboard-empty dashboard-empty--chart">
                                <span class="dashboard-empty__icon"><i class="fa fa-folder-open-o"></i></span>
                                <strong>Chưa có sản phẩm trong danh mục</strong>
                                <p>Dữ liệu phân bổ sẽ xuất hiện tại đây.</p>
                            </div>
                        @endif
                    </div>
                </article>
            </section>

            <section class="dashboard-grid dashboard-grid--tables">
                <article class="dashboard-panel">
                    <div class="dashboard-panel__header">
                        <div>
                            <span class="dashboard-panel__eyebrow">Hiệu suất</span>
                            <h2>Sản phẩm bán chạy</h2>
                        </div>
                        <span class="dashboard-count-pill">Top {{ $topSellingProducts->count() }}</span>
                    </div>

                    <div class="dashboard-panel__body dashboard-panel__body--table">
                        <div class="table-responsive dashboard-table-wrap">
                            <table class="table dashboard-table">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Đơn giá</th>
                                        <th class="text-right">Đã bán</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($topSellingProducts as $product)
                                        <tr>
                                            <td>
                                                <div class="product-cell">
                                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                                        width="48" height="48">
                                                    <div>
                                                        <strong>{{ $product->name }}</strong>
                                                        <span>#SP{{ $product->id }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="table-price">{{ number_format($product->price, 0, ',', '.') }} VNĐ</td>
                                            <td class="text-right"><span class="quantity-pill">{{ number_format($product->total_sold ?? 0) }}</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3">
                                                <div class="dashboard-empty dashboard-empty--table">Chưa có dữ liệu bán hàng.</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </article>

                <article class="dashboard-panel">
                    <div class="dashboard-panel__header">
                        <div>
                            <span class="dashboard-panel__eyebrow">Mới nhất</span>
                            <h2>Người dùng mới</h2>
                        </div>
                        <span class="dashboard-count-pill">{{ $users->count() }} người</span>
                    </div>

                    <div class="dashboard-panel__body dashboard-panel__body--table">
                        <div class="table-responsive dashboard-table-wrap">
                            <table class="table dashboard-table">
                                <thead>
                                    <tr>
                                        <th>Khách hàng</th>
                                        <th>Số điện thoại</th>
                                        <th class="text-right">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                        <tr>
                                            <td>
                                                <div class="user-cell">
                                                    <span class="user-avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                                                    <div>
                                                        <strong>{{ $user->name }}</strong>
                                                        <span>#KH{{ $user->id }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $user->phone_number ?: 'Chưa cập nhật' }}</td>
                                            <td class="text-right">
                                                @if ($user->status === 'banned')
                                                    <span class="status-badge status-badge--warning">Đã chặn</span>
                                                @elseif ($user->status === 'deleted')
                                                    <span class="status-badge status-badge--danger">Đã xóa</span>
                                                @elseif ($user->status === 'pending')
                                                    <span class="status-badge status-badge--info">Chờ kích hoạt</span>
                                                @else
                                                    <span class="status-badge status-badge--success">Đã kích hoạt</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3">
                                                <div class="dashboard-empty dashboard-empty--table">Chưa có người dùng mới.</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </article>
            </section>

            <section class="dashboard-panel dashboard-panel--orders">
                <div class="dashboard-panel__header">
                    <div>
                        <span class="dashboard-panel__eyebrow">Hoạt động gần đây</span>
                        <h2>Đơn hàng mới</h2>
                    </div>
                    <span class="dashboard-count-pill">{{ $orders->count() }} đơn gần nhất</span>
                </div>

                <div class="dashboard-panel__body dashboard-panel__body--table">
                    <div class="table-responsive dashboard-table-wrap">
                        <table class="table dashboard-table dashboard-table--orders">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày đặt</th>
                                    <th class="text-right">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $order)
                                    <tr>
                                        <td><strong class="order-code">#DH{{ $order->id }}</strong></td>
                                        <td>{{ optional($order->shippingAddress)->full_name ?? 'Chưa cập nhật' }}</td>
                                        <td class="table-price">{{ number_format($order->total_price, 0, ',', '.') }} VNĐ</td>
                                        <td>
                                            @if ($order->status === 'pending')
                                                <span class="status-badge status-badge--warning">Chờ xử lý</span>
                                            @elseif ($order->status === 'canceled')
                                                <span class="status-badge status-badge--danger">Đã hủy</span>
                                            @elseif ($order->status === 'processing')
                                                <span class="status-badge status-badge--info">Đang giao</span>
                                            @else
                                                <span class="status-badge status-badge--success">Hoàn thành</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-right">
                                            <a href="{{ route('admin.order_detail', ['id' => $order->id]) }}"
                                                class="dashboard-action" target="_blank" rel="noopener"
                                                aria-label="Xem chi tiết đơn hàng {{ $order->id }}">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                                <span>Chi tiết</span>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="dashboard-empty dashboard-empty--table">Chưa có đơn hàng mới.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/admin/js/dashboard.js') }}"></script>
@endpush
