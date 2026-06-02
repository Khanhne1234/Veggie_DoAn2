@extends('layouts.client')
@section('title', 'Câu hỏi thường gặp')
@section('breadcrumb', 'Những câu hỏi thường gặp')
@section('breadcrumb_style', 'white-space: nowrap;')

@section('content')
    <!-- FAQ AREA START (faq-2) (ID > accordion_2) -->
    <div class="ltn__faq-area mb-100">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="ltn__faq-inner ltn__faq-inner-2">
                        <div id="accordion_2">
                            <!-- card -->
                            <div class="card">
                                <h6 class="collapsed ltn__card-title" data-bs-toggle="collapse" data-bs-target="#faq-item-2-1"
                                    aria-expanded="false">
                                    Làm sao để mua sản phẩm trên Veggie?
                                </h6>
                                <div id="faq-item-2-1" class="collapse" data-parent="#accordion_2">
                                    <div class="card-body">
                                        <p>Bạn chỉ cần chọn sản phẩm, thêm vào giỏ hàng, nhập địa chỉ giao nhận và xác
                                            nhận đơn. Hệ thống sẽ gửi thông tin đơn hàng để bạn theo dõi trạng thái giao
                                            hàng dễ dàng.</p>
                                    </div>
                                </div>
                            </div>
                            <!-- card -->
                            <div class="card">
                                <h6 class="ltn__card-title" data-bs-toggle="collapse" data-bs-target="#faq-item-2-2"
                                    aria-expanded="true">
                                    Tôi có thể yêu cầu hoàn tiền như thế nào?
                                </h6>
                                <div id="faq-item-2-2" class="collapse show" data-parent="#accordion_2">
                                    <div class="card-body">
                                        <div class="ltn__video-img alignleft">
                                            <img src="{{ asset('assets/clients/img/bg/17.jpg') }}" alt="Hướng dẫn hoàn tiền">
                                            <a class="ltn__video-icon-2 ltn__video-icon-2-small ltn__video-icon-2-border----"
                                                href="https://www.youtube.com/embed/LjCzPp-MK48?autoplay=1&amp;showinfo=0"
                                                data-rel="lightcase:myCollection">
                                                <i class="fa fa-play"></i>
                                            </a>
                                        </div>
                                        <p>Nếu sản phẩm không đúng mô tả hoặc không đảm bảo chất lượng, bạn có thể gửi yêu
                                            cầu hỗ trợ trong mục liên hệ kèm mã đơn hàng. Veggie sẽ kiểm tra và hoàn tiền
                                            theo chính sách áp dụng.</p>
                                    </div>
                                </div>
                            </div>
                            <!-- card -->
                            <div class="card">
                                <h6 class="collapsed ltn__card-title" data-bs-toggle="collapse"
                                    data-bs-target="#faq-item-2-3" aria-expanded="false">
                                    Tôi là người dùng mới, bắt đầu từ đâu?
                                </h6>
                                <div id="faq-item-2-3" class="collapse" data-parent="#accordion_2">
                                    <div class="card-body">
                                        <p>Bạn hãy tạo tài khoản, cập nhật thông tin nhận hàng, sau đó khám phá danh mục
                                            sản phẩm và thêm vào giỏ hàng. Đội ngũ hỗ trợ luôn sẵn sàng nếu bạn cần tư vấn
                                            thêm.</p>
                                    </div>
                                </div>
                            </div>
                            <!-- card -->
                            <div class="card">
                                <h6 class="collapsed ltn__card-title" data-bs-toggle="collapse"
                                    data-bs-target="#faq-item-2-4" aria-expanded="false">
                                    Chính sách đổi trả và hoàn tiền
                                </h6>
                                <div id="faq-item-2-4" class="collapse" data-parent="#accordion_2">
                                    <div class="card-body">
                                        <p>Veggie hỗ trợ đổi trả với các trường hợp hàng lỗi, hư hỏng hoặc giao sai. Vui
                                            lòng liên hệ trong thời gian quy định để được xử lý nhanh chóng và minh bạch.</p>
                                    </div>
                                </div>
                            </div>
                            <!-- card -->
                            <div class="card">
                                <h6 class="collapsed ltn__card-title" data-bs-toggle="collapse"
                                    data-bs-target="#faq-item-2-5" aria-expanded="false">
                                    Thông tin cá nhân của tôi có được bảo mật không?
                                </h6>
                                <div id="faq-item-2-5" class="collapse" data-parent="#accordion_2">
                                    <div class="card-body">
                                        <p>Chúng tôi áp dụng các biện pháp bảo mật cần thiết để bảo vệ thông tin khách hàng
                                            và chỉ sử dụng dữ liệu cho mục đích xử lý đơn hàng, chăm sóc dịch vụ.</p>
                                    </div>
                                </div>
                            </div>
                            <!-- card -->
                            <div class="card">
                                <h6 class="collapsed ltn__card-title" data-bs-toggle="collapse"
                                    data-bs-target="#faq-item-2-6" aria-expanded="false">
                                    Mã giảm giá không áp dụng được
                                </h6>
                                <div id="faq-item-2-6" class="collapse" data-parent="#accordion_2">
                                    <div class="card-body">
                                        <p>Vui lòng kiểm tra điều kiện chương trình, thời hạn sử dụng và giá trị đơn tối
                                            thiểu. Nếu vẫn gặp lỗi, hãy liên hệ Veggie để được hỗ trợ ngay.</p>
                                    </div>
                                </div>
                            </div>
                            <!-- card -->
                            <div class="card">
                                <h6 class="collapsed ltn__card-title" data-bs-toggle="collapse"
                                    data-bs-target="#faq-item-2-7" aria-expanded="false">
                                    Tôi có thể thanh toán bằng thẻ tín dụng không?
                                </h6>
                                <div id="faq-item-2-7" class="collapse" data-parent="#accordion_2">
                                    <div class="card-body">
                                        <p>Có. Bạn có thể chọn thanh toán bằng thẻ tín dụng/ghi nợ hoặc các phương thức
                                            thanh toán trực tuyến khác được hỗ trợ tại bước xác nhận đơn hàng.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="need-support text-center mt-100">
                            <h2>Bạn vẫn cần hỗ trợ? Liên hệ đội ngũ chăm sóc khách hàng:</h2>
                            <div class="btn-wrapper mb-30">
                                <a href="contact.html" class="theme-btn-1 btn">Liên hệ ngay</a>
                            </div>
                            <h3><i class="fas fa-phone"></i> +0123-456-789</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <aside class="sidebar-area ltn__right-sidebar">
                        <!-- Newsletter Widget -->
                        <div class="widget ltn__search-widget ltn__newsletter-widget">
                            <h6 class="ltn__widget-sub-title">// đăng ký</h6>
                            <h4 class="ltn__widget-title">Nhận bản tin</h4>
                            <form action="#">
                                <input type="text" name="search" placeholder="Tìm kiếm">
                                <button type="submit"><i class="fas fa-search"></i></button>
                            </form>
                            <div class="ltn__newsletter-bg-icon">
                                <i class="fas fa-envelope-open-text"></i>
                            </div>
                        </div>
                        <!-- Banner Widget -->
                        <div class="widget ltn__banner-widget">
                            <a href="shop.html"><img src="{{ asset('assets/clients/img/banner/banner-3.jpg') }}" alt="Banner Image"></a>
                        </div>

                    </aside>
                </div>
            </div>
        </div>
    </div>
    <!-- FAQ AREA START -->

    <!-- COUNTER UP AREA START -->
    <div class="ltn__counterup-area bg-image bg-overlay-theme-black-80 pt-115 pb-70" data-bg="{{ asset('assets/clients/img/bg/5.jpg') }}">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-6 align-self-center">
                    <div class="ltn__counterup-item-3 text-color-white text-center">
                        <div class="counter-icon"> <img src="{{ asset('assets/clients/img/icons/icon-img/2.png') }}" alt="#"> </div>
                        <h1><span class="counter">733</span><span class="counterUp-icon">+</span> </h1>
                        <h6>Khách hàng thường xuyên</h6>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 align-self-center">
                    <div class="ltn__counterup-item-3 text-color-white text-center">
                        <div class="counter-icon"> <img src="{{ asset('assets/clients/img/icons/icon-img/3.png') }}" alt="#"> </div>
                        <h1><span class="counter">33</span><span class="counterUp-letter">K</span><span
                                class="counterUp-icon">+</span> </h1>
                        <h6>Đơn hàng đã xử lý</h6>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 align-self-center">
                    <div class="ltn__counterup-item-3 text-color-white text-center">
                        <div class="counter-icon"> <img src="{{ asset('assets/clients/img/icons/icon-img/4.png') }}" alt="#"> </div>
                        <h1><span class="counter">100</span><span class="counterUp-icon">+</span> </h1>
                        <h6>Đối tác cung ứng</h6>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 align-self-center">
                    <div class="ltn__counterup-item-3 text-color-white text-center">
                        <div class="counter-icon"> <img src="{{ asset('assets/clients/img/icons/icon-img/5.png') }}" alt="#"> </div>
                        <h1><span class="counter">21</span><span class="counterUp-icon">+</span> </h1>
                        <h6>Khu vực phục vụ</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- COUNTER UP AREA END -->


@endsection
