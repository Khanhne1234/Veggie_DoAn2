@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')

    <!-- page content -->
    <div class="right_col" role="main">
        <!-- top tiles -->
        <div class="row" style="display: inline-block;">
            <div class="tile_count">
                <div class="col-md-3 col-sm-6  tile_stats_count">
                    <span class="count_top"><i class="fa fa-user"></i>Tổng số người dùng</span>
                    <div class="count">{{ $users->count() }}</div>
                </div>
                <div class="col-md-3 col-sm-6  tile_stats_count">
                    <span class="count_top"><i class="fa fa-bar-chart"></i>Tổng số lượng sản phẩm</span>
                    <div class="count">{{ $products->count() }}</div>
                </div>
                <div class="col-md-3 col-sm-6  tile_stats_count">
                    <span class="count_top"><i class="fa fa-shopping-cart"></i> Tổng đơn hàng</span>
                    <div class="count green">{{ $orders->count() }}</div>
                </div>
                <div class="col-md-3 col-sm-6  tile_stats_count" style="overflow: unset;">
                    <span class="count_top"><i class="fa fa-money"></i> Tổng doanh thu</span>
                    <div class="count">
                        {{ number_format($orders->sum('total_price'), 0, ',', '.') }}VNĐ</div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Revenue by month-->

            <div class="col-md-4 col-sm-4  ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Doanh thu</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <canvas id="revenueBarChart" data-labels='@json($monthlyRevenue->pluck('month')->toArray())'
                            data-values='@json($monthlyRevenue->pluck('total_revenue')->toArray())' style="margin: 15px 10px 10px 0"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 ">
                <div class="x_panel tile fixed_height_320 overflow_hidden">
                    <div class="x_title">
                        <h2>Danh mục</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <table class="" style="width:100%">
                            <tr>
                                <th style="width:37%;">
                                    <p>Top 5</p>
                                </th>
                                <th>
                                    <div class="col-lg-7 col-md-7 col-sm-7 ">
                                        <p class="">Danh mục</p>
                                    </div>
                                    <div class="col-lg-5 col-md-5 col-sm-5 ">
                                        <p class="">Sản phẩm</p>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <canvas class="canvasDoughnutCategory" height="140" width="140"
                                        data-labels='@json($categories->pluck('name'))'
                                        data-counts='@json($categories->map(fn($category) => $category->products->count()))'
                                        style="margin: 15px 10px 10px 0"></canvas>
                                </td>
                                <td>
                                    <table class="tile_info">
                                        @foreach ($categories as $index => $category)
                                            <tr>
                                                <td>
                                                    <p><i class="fa fa-square"
                                                            style="color: {{ ['#BDC3C7', '#9B59B6', '#E74C3C', '#26B99A', '#3498DB', '#F39C12', '#8E44AD', '#1ABC9C', '#C0392B', '#2ECC71'][$index % 10] }}"></i>
                                                        {{ $category->name }} </p>
                                                </td>
                                                <td>{{ $category->products->count() }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Table Top 3 product selling-->
            <div class="col-md-5 col-sm-5  ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Sản phẩm bán chạy nhất <small>Danh sách</small></h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Số lượng đã bán</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topSellingProducts as $index => $product)
                                    <tr>
                                        <th scope="row">{{ $product->id }}</th>
                                        <td><img src="{{ $product->image_url }}" alt="{{ $product->name }}" width="50"
                                                height="50"></td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ number_format($product->price, 0, ',', '.') }}VNĐ</td>
                                        <td>{{ $product->total_sold }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            <!-- Table User-->
            <div class="col-md-6 col-sm-6  ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Người dùng mới <small>Danh sách</small></h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">

                        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Khách hàng</th>
                                        <th>Số điện thoại</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users->sortByDesc('id') as $user)
                                        <tr>
                                            <td scope="row">{{ $user->id }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->phone_number }}</td>
                                            <td>
                                                @if ($user->status == 'banned')
                                                    <span class="badge bg-warning">Đã chặn</span>
                                                @elseif ($user->status == 'deleted')
                                                    <span class="badge bg-danger">Đã xóa</span>
                                                @elseif($user->status == 'pending')
                                                    <span class="badge bg-primary">Chờ kích hoạt</span>
                                                @else
                                                    <span class="badge bg-success">Đã kích hoạt</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            <!-- Table User-->
            <div class="col-md-8 col-sm-8  ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Đơn hàng mới <small>Danh sách</small></h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Khách hàng</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày đặt hàng</th>
                                    <th>Xem chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < min($orders->count(), 5); $i++)
                                    <tr>
                                        <td scope="row">{{ $orders[$i]->id }}</td>
                                        <td>{{ $orders[$i]->shippingAddress->full_name }}</td>
                                        <td>{{ number_format($orders[$i]->total_price, 0, ',', '.') }}VNĐ</td>
                                        <td>
                                            @if ($orders[$i]->status == 'pending')
                                                <span class="badge bg-warning">Chờ xử lý</span>
                                            @elseif ($orders[$i]->status == 'canceled')
                                                <span class="badge bg-danger">Đã hủy</span>
                                            @elseif($orders[$i]->status == 'processing')
                                                <span class="badge bg-info">Đang giao</span>
                                            @else
                                                <span class="badge bg-success">Hoàn thành</span>
                                            @endif
                                        </td>
                                        <td>{{ $orders[$i]->created_at->format('d-m-y H:i:s') }}</td>
                                        <td>
                                            <a href="{{ route('admin.order_detail', ['id' => $orders[$i]->id]) }}"
                                                class="btn btn-primary" target="_blank">Xem chi tiết</a>
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /page content -->
@endsection
