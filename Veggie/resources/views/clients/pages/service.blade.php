@extends('layouts.client')
@section('title', 'Dịch vụ')
@section('breadcrumb', 'Dịch vụ')

@section('content')

    <!-- ABOUT US AREA START -->
    <div class="ltn__about-us-area pb-115">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 align-self-center">
                    <div class="about-us-img-wrap ltn__img-shape-left  about-img-left">
                        <img src="{{ asset('assets/clients/img/service/11.jpg') }}" alt="Dịch vụ của Veggie">
                    </div>
                </div>
                <div class="col-lg-7 align-self-center">
                    <div class="about-us-info-wrap">
                        <div class="section-title-area ltn__section-title-2">
                            <h6 class="section-subtitle ltn__secondary-color">// dịch vụ tin cậy</h6>
                            <h1 class="section-title">Dịch vụ chuyên nghiệp
                                cho gian bếp Việt<span>.</span></h1>
                            <p>Veggie cung cấp giải pháp mua sắm thực phẩm tiện lợi, nhanh chóng và an toàn cho mọi gia
                                đình.</p>
                        </div>
                        <div class="about-us-info-wrap-inner about-us-info-devide">
                            <p>Từ khâu tuyển chọn nguồn hàng đến giao nhận tận nơi, chúng tôi luôn đặt chất lượng sản phẩm
                                và trải nghiệm khách hàng lên hàng đầu. Đội ngũ Veggie không ngừng tối ưu quy trình để
                                giúp bạn mua thực phẩm mỗi ngày một cách dễ dàng, tiết kiệm thời gian và an tâm hơn.</p>
                            <div class="list-item-with-icon">
                                <ul>
                                    <li><a href="{{ route('contact.index') }}">Giao hàng tận nơi linh hoạt</a></li>
                                    <li><a href="{{ route('team') }}">Đội ngũ hỗ trợ tận tâm</a></li>
                                    <li><a href="{{ route('service') }}">Kiểm soát chất lượng chặt chẽ</a></li>
                                    <li><a href="{{ route('products.index') }}">Danh mục sản phẩm đa dạng</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ABOUT US AREA END -->

    <!-- SERVICE AREA START (Service 1) -->
    <div class="ltn__service-area section-bg-1 pt-115 pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area ltn__section-title-2 text-center">
                        <h1 class="section-title white-color---">Dịch vụ của chúng tôi</h1>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <a href="{{ route('service') }}"><img src="{{ asset('assets/clients/img/service/1.jpg') }}"
                                    alt="#"></a>
                        </div>
                        <div class="service-item-brief">
                            <h3><a href="{{ route('service') }}">Rau củ tươi theo ngày</a></h3>
                            <p>Cung cấp rau củ sạch, cập nhật mỗi ngày từ nguồn trồng đạt tiêu chuẩn.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <a href="{{ route('service') }}"><img src="{{ asset('assets/clients/img/service/2.jpg') }}"
                                    alt="#"></a>
                        </div>
                        <div class="service-item-brief">
                            <h3><a href="{{ route('service') }}">Đóng gói bảo quản kỹ</a></h3>
                            <p>Sản phẩm được phân loại và đóng gói cẩn thận để giữ độ tươi ngon khi vận chuyển.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <a href="{{ route('service') }}"><img src="{{ asset('assets/clients/img/service/3.jpg') }}"
                                    alt="#"></a>
                        </div>
                        <div class="service-item-brief">
                            <h3><a href="{{ route('service') }}">Giao nhanh trong ngày</a></h3>
                            <p>Hỗ trợ giao hàng nhanh giúp bạn chuẩn bị bữa ăn đúng giờ, không phải chờ đợi lâu.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <a href="{{ route('service') }}"><img src="{{ asset('assets/clients/img/service/3.jpg') }}"
                                    alt="#"></a>
                        </div>
                        <div class="service-item-brief">
                            <h3><a href="{{ route('service') }}">Tư vấn thực phẩm phù hợp</a></h3>
                            <p>Gợi ý sản phẩm theo nhu cầu dinh dưỡng và khẩu vị cho từng thành viên trong gia đình.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <a href="{{ route('service') }}"><img src="{{ asset('assets/clients/img/service/1.jpg') }}"
                                    alt="#"></a>
                        </div>
                        <div class="service-item-brief">
                            <h3><a href="{{ route('service') }}">Ưu đãi thành viên định kỳ</a></h3>
                            <p>Nhiều chương trình khuyến mãi theo tuần giúp bạn tiết kiệm chi phí mua sắm thực phẩm.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <a href="{{ route('service') }}"><img src="{{ asset('assets/clients/img/service/2.jpg') }}"
                                    alt="#"></a>
                        </div>
                        <div class="service-item-brief">
                            <h3><a href="{{ route('service') }}">Hỗ trợ đổi trả minh bạch</a></h3>
                            <p>Chính sách đổi trả rõ ràng nếu sản phẩm không đạt chất lượng như cam kết.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- SERVICE AREA END -->

    <!-- OUR JOURNEY AREA START -->
    <div class="ltn__our-journey-area bg-image bg-overlay-theme-90 pt-280 pb-350 mb-35 plr--9"
        data-bg="{{ asset('assets/clients/img/bg/8.jpg') }}">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ltn__our-journey-wrap ">
                        <ul>
                            <li><span class="ltn__journey-icon">2018</span>
                                <ul>
                                    <li>
                                        <div class="ltn__journey-history-item-info clearfix">
                                            <div class="ltn__journey-history-img">
                                                <img src="{{ asset('assets/clients/img/service/history-1.jpg') }}"
                                                    alt="#">
                                            </div>
                                            <div class="ltn__journey-history-info">
                                                <h3>Khởi đầu hành trình</h3>
                                                <p>Veggie bắt đầu với mong muốn đưa thực phẩm sạch đến gần hơn với mọi gia
                                                    đình.</p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li class="active"><span class="ltn__journey-icon">2020</span>
                                <ul>
                                    <li>
                                        <div class="ltn__journey-history-item-info clearfix">
                                            <div class="ltn__journey-history-img">
                                                <img src="{{ asset('assets/clients/img/service/history-1.jpg') }}"
                                                    alt="#">
                                            </div>
                                            <div class="ltn__journey-history-info">
                                                <h3>Mở rộng danh mục</h3>
                                                <p>Bổ sung thêm hàng trăm sản phẩm thiết yếu phục vụ nhu cầu mua sắm hằng
                                                    ngày.</p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li><span class="ltn__journey-icon">2022</span>
                                <ul>
                                    <li>
                                        <div class="ltn__journey-history-item-info clearfix">
                                            <div class="ltn__journey-history-img">
                                                <img src="{{ asset('assets/clients/img/service/history-1.jpg') }}"
                                                    alt="#">
                                            </div>
                                            <div class="ltn__journey-history-info">
                                                <h3>Nâng cấp vận hành</h3>
                                                <p>Tối ưu kho và giao nhận giúp rút ngắn thời gian xử lý đơn hàng đáng kể.
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li><span class="ltn__journey-icon">2024</span>
                                <ul>
                                    <li>
                                        <div class="ltn__journey-history-item-info clearfix">
                                            <div class="ltn__journey-history-img">
                                                <img src="{{ asset('assets/clients/img/service/history-1.jpg') }}"
                                                    alt="#">
                                            </div>
                                            <div class="ltn__journey-history-info">
                                                <h3>Phục vụ đa kênh</h3>
                                                <p>Đồng bộ website và chăm sóc khách hàng để nâng cao trải nghiệm mua sắm
                                                    trực tuyến.</p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li><span class="ltn__journey-icon">2026</span>
                                <ul>
                                    <li>
                                        <div class="ltn__journey-history-item-info clearfix">
                                            <div class="ltn__journey-history-img">
                                                <img src="{{ asset('assets/clients/img/service/history-1.jpg') }}"
                                                    alt="#">
                                            </div>
                                            <div class="ltn__journey-history-info">
                                                <h3>Tiếp tục phát triển</h3>
                                                <p>Mở rộng mạng lưới đối tác nông trại để mang đến nguồn thực phẩm xanh và
                                                    bền vững.</p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- OUR JOURNEY AREA END -->

@endsection
