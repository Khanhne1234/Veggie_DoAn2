@extends('layouts.client')
@section('title', 'Về chúng tôi')
@section('breadcrumb', 'Về chúng tôi')

@section('content')

    <!-- ABOUT US AREA START -->
    <div class="ltn__about-us-area pt-120--- pb-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 align-self-center">
                    <div class="about-us-img-wrap about-img-left">
                        <img src="{{ asset('assets/clients/img/others/6.png') }}" alt="Hình ảnh về Veggie">
                    </div>
                </div>
                <div class="col-lg-6 align-self-center">
                    <div class="about-us-info-wrap">
                        <div class="section-title-area ltn__section-title-2">
                            <h6 class="section-subtitle ltn__secondary-color">Tìm hiểu thêm về cửa hàng</h6>
                            <h1 class="section-title">Cửa hàng thực phẩm <br class="d-none d-md-block"> hữu cơ uy tín</h1>
                            <p>Veggie mang đến thực phẩm tươi ngon, rõ nguồn gốc và an toàn cho mọi bữa ăn gia đình.</p>
                        </div>
                        <p>Chúng tôi hợp tác cùng các nông trại và nhà cung cấp đáng tin cậy để tuyển chọn rau củ,
                            trái cây, thực phẩm thiết yếu mỗi ngày. Với quy trình kiểm soát chất lượng chặt chẽ,
                            Veggie giúp bạn mua sắm nhanh chóng, tiết kiệm và luôn an tâm.</p>
                        <div class="about-author-info d-flex">
                            <div class="author-name-designation  align-self-center">
                                <h4 class="mb-0">Nguyễn Minh An</h4>
                                <small>/ Giám đốc cửa hàng</small>
                            </div>
                            <div class="author-sign">
                                <img src="{{ asset('assets/clients/img/icons/icon-img/author-sign.png') }}" alt="#">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ABOUT US AREA END -->

    <!-- FEATURE AREA START ( Feature - 6) -->
    <div class="ltn__feature-area section-bg-1 pt-115 pb-90">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area ltn__section-title-2 text-center">
                        <h6 class="section-subtitle ltn__secondary-color">// điểm nổi bật //</h6>
                        <h1 class="section-title">Vì sao nên chọn chúng tôi<span>.</span></h1>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="ltn__feature-item ltn__feature-item-7">
                        <div class="ltn__feature-icon-title">
                            <div class="ltn__feature-icon">
                                <span><img src="{{ asset('assets/clients/img/icons/icon-img/21.png') }}" alt="#"></span>
                            </div>
                            <h3><a href="service-details.html">Đa dạng sản phẩm</a></h3>
                        </div>
                        <div class="ltn__feature-info">
                            <p>Từ rau củ, trái cây, thực phẩm sạch đến đồ dùng bếp, đáp ứng đầy đủ nhu cầu mua sắm mỗi ngày.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="ltn__feature-item ltn__feature-item-7">
                        <div class="ltn__feature-icon-title">
                            <div class="ltn__feature-icon">
                                <span><img src="{{ asset('assets/clients/img/icons/icon-img/22.png') }}" alt="#"></span>
                            </div>
                            <h3><a href="service-details.html">Sản phẩm tuyển chọn</a></h3>
                        </div>
                        <div class="ltn__feature-info">
                            <p>Mỗi sản phẩm đều được kiểm tra nguồn gốc, hạn sử dụng và tiêu chuẩn chất lượng trước khi giao đến bạn.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="ltn__feature-item ltn__feature-item-7">
                        <div class="ltn__feature-icon-title">
                            <div class="ltn__feature-icon">
                                <span><img src="{{ asset('assets/clients/img/icons/icon-img/23.png') }}" alt="#"></span>
                            </div>
                            <h3><a href="service-details.html">An toàn cho sức khỏe</a></h3>
                        </div>
                        <div class="ltn__feature-info">
                            <p>Ưu tiên thực phẩm sạch, tươi mới hằng ngày, góp phần mang lại bữa ăn lành mạnh cho gia đình bạn.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- FEATURE AREA END -->

@endsection
