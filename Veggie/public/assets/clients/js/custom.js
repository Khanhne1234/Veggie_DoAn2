$(document).ready(function () {
    // Hiển thị thông báo toastr nếu có session registerSuccess
    $("#register-form").submit(function (event) {
        let name = $('input[name="name"]').val();
        let email = $('input[name="email"]').val();
        let password = $('input[name="password"]').val();
        let confirmpassword = $('input[name="confirmpassword"]').val();
        let checkbox1 = $('input[name="checkbox1"]').is(":checked");
        let checkbox2 = $('input[name="checkbox2"]').is(":checked");

        let errorsMessage = [];
        // Kiểm tra điều kiện và thêm thông báo lỗi vào mảng errorsMessage
        if (name.length < 3) {
            errorsMessage.push("Họ và tên phải có ít nhất 3 ký tự.");
        }
        // Sử dụng biểu thức chính quy để kiểm tra định dạng email
        let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            errorsMessage.push("Email không hợp lệ.");
        }
        // Kiểm tra độ dài mật khẩu
        if (password.length < 6) {
            errorsMessage.push("Mật khẩu phải có ít nhất 6 ký tự.");
        }
        // Kiểm tra xác nhận mật khẩu
        if (password !== confirmpassword) {
            errorsMessage.push("Xác nhận mật khẩu không khớp.");
        }
        // Kiểm tra checkbox
        if (!checkbox1) {
            errorsMessage.push(
                "Bạn phải đồng ý cho Veggie xử lý dữ liệu cá nhân.",
            );
        }
        // Kiểm tra checkbox
        if (!checkbox2) {
            errorsMessage.push("Bạn phải đồng ý với chính sách bảo mật.");
        }

        if (errorsMessage.length > 0) {
            toastr.error(errorsMessage.join("<br>"), "Lỗi đăng ký", {
                timeOut: 5000,
                extendedTimeOut: 2000,
                closeButton: true,
                progressBar: true,
            });
            event.preventDefault(); // Ngăn chặn gửi form nếu có lỗi
        }
    });

    // Hiển thị thông báo toastr nếu có session loginError
    $("#login-form").submit(function (event) {
        let email = $('input[name="email"]').val();
        let password = $('input[name="password"]').val();

        let errorsMessage = [];
        // Sử dụng biểu thức chính quy để kiểm tra định dạng email
        let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            errorsMessage.push("Email không hợp lệ.");
        }
        // Kiểm tra độ dài mật khẩu
        if (password.length < 6) {
            errorsMessage.push("Mật khẩu phải có ít nhất 6 ký tự.");
        }

        if (errorsMessage.length > 0) {
            toastr.error(errorsMessage.join("<br>"), "Lỗi đăng ký", {
                timeOut: 5000,
                extendedTimeOut: 2000,
                closeButton: true,
                progressBar: true,
            });
            event.preventDefault(); // Ngăn chặn gửi form nếu có lỗi
        }
    });

    // Hiển thị thông báo toastr nếu có session ResetPasswordSuccess
    $("#reset-password-form").submit(function (event) {
        let email = $('input[name="email"]').val();
        let password = $('input[name="password"]').val();
        let password_confirmation = $(
            'input[name="password_confirmation"]',
        ).val();

        let errorsMessage = [];
        // Sử dụng biểu thức chính quy để kiểm tra định dạng email
        let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            errorsMessage.push("Email không hợp lệ.");
        }
        // Kiểm tra độ dài mật khẩu
        if (password.length < 6) {
            errorsMessage.push("Mật khẩu phải có ít nhất 6 ký tự.");
        }
        // Kiểm tra xác nhận mật khẩu
        if (password !== password_confirmation) {
            errorsMessage.push("Xác nhận mật khẩu không khớp.");
        }

        if (errorsMessage.length > 0) {
            toastr.error(errorsMessage.join("<br>"), "Lỗi đăng ký", {
                timeOut: 5000,
                extendedTimeOut: 2000,
                closeButton: true,
                progressBar: true,
            });
            event.preventDefault(); // Ngăn chặn gửi form nếu có lỗi
        }
    });

    // When clicking on the avatar preview, trigger the hidden file input.
    $("#preview-image, .profile-pic").on("click", function () {
        $("#avatar").trigger("click");
    });

    //When select image, show preview
    $("#avatar").change(function () {
        let input = this;
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $("#preview-image").attr("src", e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    });

    //Page Account
    $("#update-account").submit(function (event) {
        event.preventDefault(); // Ngăn chặn gửi form mặc định

        let $form = $(this);
        let $submitButton = $form.find('button[type="submit"]');
        let originalButtonText = $submitButton.text().trim();
        let formData = new FormData(this); // Tạo FormData từ form
        let urlUpdate = $form.attr("action"); // Lấy URL từ thuộc tính action của form
        $.ajax({
            url: urlUpdate,
            type: "POST",
            data: formData,
            processData: false, // Không xử lý dữ liệu
            contentType: false, // Không đặt contentType
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            beforeSend: function () {
                $submitButton.text("Đang cập nhật...").attr("disabled", true);
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message, "Thành công", {
                        timeOut: 5000,
                        extendedTimeOut: 2000,
                        closeButton: true,
                        progressBar: true,
                    });
                    // Cập nhật ảnh đại diện nếu có
                    if (response.avatar) {
                        $("#preview-image").attr("src", response.avatar);
                    }
                } else {
                    toastr.error("Cập nhật thông tin thất bại!", "Lỗi", {
                        timeOut: 5000,
                        extendedTimeOut: 2000,
                        closeButton: true,
                        progressBar: true,
                    });
                }
            },
            error: function (xhr, status, error) {
                let message = "Cập nhật thông tin thất bại!";
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let key in errors) {
                        if (errors.hasOwnProperty(key)) {
                            errorMessages.push(errors[key].join("<br>"));
                        }
                    }
                    if (errorMessages.length > 0) {
                        message = errorMessages.join("<br>");
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                toastr.error(message, "Lỗi", {
                    timeOut: 5000,
                    extendedTimeOut: 2000,
                    closeButton: true,
                    progressBar: true,
                });
            },
            complete: function () {
                $submitButton.text(originalButtonText).attr("disabled", false);
            },
        });
    });

    // change password form
    $("#change-password-form").submit(function (event) {
        event.preventDefault(); // Ngăn chặn gửi form mặc định
        let $form = $(this);
        let $submitButton = $form.find('button[type="submit"]');
        let originalButtonText = $submitButton.text().trim();
        let current_password = $('input[name="current_password"]').val().trim();
        let new_password = $('input[name="new_password"]').val().trim();
        let confirm_password = $('input[name="new_password_confirmation"]')
            .val()
            .trim();

        let errorsMessage = [];
        // Kiểm tra độ dài mật khẩu
        if (current_password.length < 6) {
            errorsMessage.push("Mật khẩu hiện tại phải có ít nhất 6 ký tự.");
        }
        // Kiểm tra độ dài mật khẩu mới
        if (new_password.length < 6) {
            errorsMessage.push("Mật khẩu mới phải có ít nhất 6 ký tự.");
        }
        // Kiểm tra xác nhận mật khẩu
        if (new_password !== confirm_password) {
            errorsMessage.push("Xác nhận mật khẩu mới không khớp.");
        }

        if (errorsMessage.length > 0) {
            toastr.error(errorsMessage.join("<br>"), "Lỗi đăng ký", {
                timeOut: 5000,
                extendedTimeOut: 2000,
                closeButton: true,
                progressBar: true,
            });
            return; // Dừng xử lý nếu có lỗi
        }

        let formData = $(this).serialize(); // Tạo FormData từ form
        let urlChangePassword = $form.attr("action"); // Lấy URL từ thuộc tính action của form

        $.ajax({
            url: urlChangePassword,
            type: "POST",
            data: formData,
            beforeSend: function () {
                $submitButton.text("Đang cập nhật...").attr("disabled", true);
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message, "Thành công", {
                        timeOut: 5000,
                        extendedTimeOut: 2000,
                        closeButton: true,
                        progressBar: true,
                    });
                    // Reset form sau khi đổi mật khẩu thành công
                    $("#change-password-form")[0].reset();
                } else {
                    toastr.error(response.message, "Lỗi", {
                        timeOut: 5000,
                        extendedTimeOut: 2000,
                        closeButton: true,
                        progressBar: true,
                    });
                }
            },
            error: function (xhr, status, error) {
                let serverMessage = "Đổi mật khẩu thất bại!";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    serverMessage = xhr.responseJSON.message;
                }

                toastr.error(serverMessage, "Lỗi", {
                    timeOut: 5000,
                    extendedTimeOut: 2000,
                    closeButton: true,
                    progressBar: true,
                });
            },
            complete: function () {
                $submitButton.text(originalButtonText).attr("disabled", false);
            },
        });
    });

    // add address form
    $("#addAddressForm").submit(function (event) {
        event.preventDefault(); // Ngăn chặn gửi form mặc định
        let isValid = true;
        const $form = $(this);
        const $submitButton = $form.find('button[type="submit"]');
        const originalButtonText = $submitButton.text();

        //delete old error message
        $form.find(".text-danger").remove();
        let fullName = $form.find('input[name="full_name"]').val().trim();
        let phone = $form.find('input[name="phone"]').val().trim();
        let city = $form.find('input[name="city"]').val().trim();
        let address = $form.find('input[name="address"]').val().trim();

        if (fullName.length < 3) {
            isValid = false;
            $form
                .find('input[name="full_name"]')
                .after(
                    '<div class="text-danger mt-1">Họ và tên phải có ít nhất 3 ký tự.</div>',
                );
        }
        let phonePattern = /^\d{10,15}$/;
        if (!phonePattern.test(phone)) {
            isValid = false;
            $form
                .find('input[name="phone"]')
                .after(
                    '<div class="text-danger mt-1">Số điện thoại không hợp lệ.</div>',
                );
        }
        if (!city) {
            isValid = false;
            $form
                .find('input[name="city"]')
                .after(
                    '<div class="text-danger mt-1">Vui lòng nhập thành phố.</div>',
                );
        }
        if (address.length < 5) {
            isValid = false;
            $form
                .find('input[name="address"]')
                .after(
                    '<div class="text-danger mt-1">Địa chỉ phải có ít nhất 5 ký tự.</div>',
                );
        }

        if (!isValid) {
            return;
        }

        let formData = $form.serialize();
        let urlAddAddress = $form.attr("action");

        $.ajax({
            url: urlAddAddress,
            type: "POST",
            data: formData,
            beforeSend: function () {
                $submitButton.text("Đang lưu...").attr("disabled", true);
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message, "Thành công", {
                        timeOut: 3000,
                        extendedTimeOut: 1000,
                        closeButton: true,
                        progressBar: true,
                    });

                    const modalElement =
                        document.getElementById("addAddressModal");
                    const modalInstance =
                        bootstrap.Modal.getInstance(modalElement);
                    if (modalInstance) {
                        modalInstance.hide();
                    }

                    setTimeout(function () {
                        window.location.reload();
                    }, 600);
                }
            },
            error: function (xhr) {
                let message = "Lưu địa chỉ thất bại!";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                toastr.error(message, "Lỗi", {
                    timeOut: 5000,
                    extendedTimeOut: 2000,
                    closeButton: true,
                    progressBar: true,
                });
            },
            complete: function () {
                $submitButton.text(originalButtonText).attr("disabled", false);
            },
        });
    });
    /**********************************
     * *PAGE PRODUCT
     * **************************/
    // Pagination click event
    let currentPage = 1;
    $(document).on("click", ".pagination-link", function (e) {
        e.preventDefault();
        let pageUrl = $(this).attr("href");
        let page = pageUrl.split("page=")[1]; // Lấy số trang từ URL
        currentPage = page;
        fetchProducts();
    });
    //Product loading function(combining filters and pagination)
    function fetchProducts() {
        let category_id = $(".category-filter.active").data("id") || "";
        let min_price = $(".slider-range").slider("values", 0);
        let max_price = $(".slider-range").slider("values", 1);
        let sort_by = $("#sort-by").val();
        $.ajaxSetup({
            Headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "/products/filter?page=" + currentPage,
            type: "GET",
            data: {
                category_id: category_id,
                min_price: min_price,
                max_price: max_price,
                sort_by: sort_by,
            },
            beforeSend: function () {
                $("#loading-spinner").show();
                $("#liton_product_grid").hide();
            },
            success: function (response) {
                $("#liton_product_grid").html(response.products);
                $(".ltn__pagination").html(response.pagination);
            },
            complete: function () {
                $("#loading-spinner").hide();
                $("#liton_product_grid").show();
            },
            error: function (xhr, status, error) {
                alert("Lỗi khi tải sản phẩm: " + error);
            },
        });
    }
    $(".category-filter").click(function () {
        $(".category-filter").removeClass("active");
        $(this).addClass("active");
        currentPage = 1; // Reset về trang đầu khi thay đổi bộ lọc
        fetchProducts();
    });
    $("#sort-by").change(function () {
        fetchProducts();
    });
    function formatVnd(value) {
        return Number(value || 0).toLocaleString("vi-VN");
    }
    // Price range slider
    $(".slider-range").slider({
        range: true,
        min: 0,
        max: 300000,
        values: [0, 300000],
        slide: function (event, ui) {
            $(".amount").val(
                formatVnd(ui.values[0]) +
                " - " +
                formatVnd(ui.values[1]) +
                " VNĐ",
            );
        },
        change: function (event, ui) {
            currentPage = 1; // Reset về trang đầu khi thay đổi bộ lọc
            fetchProducts();
        },
    });
    $(".amount").val(
        formatVnd($(".slider-range").slider("values", 0)) +
        " - " +
        formatVnd($(".slider-range").slider("values", 1)) +
        " VNĐ",
    );

    /**********************************
     * *PAGE DETAIL PRODUCT
     * **************************/
    //Kiểm tra số lượng khi click vào nút tăng giảm
    if (window.location.pathname !== "/cart") {
        $(document).on("click", ".qtybutton", function () {
            let button = $(this);
            let $input = $(this).siblings(".cart-plus-minus-box");
            let currentVal = parseInt($input.val());
            let maxVal = parseInt($input.data("max"));
            if ($(this).hasClass("inc")) {
                if (currentVal < maxVal) {
                    $input.val(currentVal + 1);
                }
            } else {
                if (currentVal > 1) {
                    $input.val(currentVal - 1);
                }
            }
        });
    } else {
        $(document).on("click", ".qtybutton", function () {
            let $button = $(this);
            let $input = $button.siblings(".cart-plus-minus-box");
            let currentVal = parseInt($input.val());
            let maxVal = parseInt($input.data("max"));
            let productId = $input.data("id");
            let newValue = currentVal;
            if ($button.hasClass("inc") && currentVal < maxVal) {
                newValue = currentVal + 1;
            } else if ($button.hasClass("dec") && currentVal > 1) {
                newValue = currentVal - 1;
            }

            if (newValue !== currentVal) {
                updatCart(productId, newValue, $input);
                $input.val(newValue);
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content",
                        ),
                    },
                });
            }
        });
    }

    //Add to cart
    $(document).on("click", ".add-to-cart-btn", function (e) {
        e.preventDefault();
        let productId = $(this).data("id");
        let quantity = $(this)
            .closest("li")
            .prev()
            .find(".cart-plus-minus-box")
            .val();
        quantity = quantity ? parseInt(quantity) : 1; // Mặc định số lượng là 1 nếu không có giá trị
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "/cart/add",
            type: "POST",
            data: {
                product_id: productId,
                quantity: quantity,
            },
            success: function (response) {
                $("#add_to_cart_modal-" + productId).modal("show");
                $("#quick_view_modal-" + productId).modal("hide");
                $("#cart_count").text(response.cart_count);
            },
            error: function (xhr, status, error) {
                alert("Lỗi khi thêm vào giỏ hàng: " + error);
            },
        });
    });

    /**********************************
     * *Cart page
     * **************************/
    //Mini cart page - update quantity
    $(".mini-cart-icon").on("click", function (e) {
        $.ajax({
            url: "/mini-cart",
            type: "GET",
            success: function (response) {
                if (response.status) {
                    $("#ltn__utilize-cart-menu .ltn__utilize-menu-inner").html(
                        response.html,
                    );
                    $("#ltn__utilize-cart-menu").addClass("ltn__utilize-open");
                } else {
                    toastr.error("Lỗi khi cập nhật giỏ hàng!", "Lỗi", {
                        timeOut: 5000,
                        extendedTimeOut: 2000,
                        closeButton: true,
                        progressBar: true,
                    });
                }
            },
        });
    });
    //Đóng mini cart
    $(document).on("click", ".ltn__utilize-close", function () {
        $("#ltn__utilize-cart-menu").removeClass("ltn__utilize-open");
        $(".ltn__utilize-overlay").hide();
    });
    //Xóa sản phẩm khỏi giỏ hàng
    $(document).on("click", ".mini-cart-item-delete", function () {
        let productId = $(this).data("id");
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "/cart/remove",
            type: "POST",
            data: { product_id: productId },
            success: function (response) {
                if (response.status) {
                    $("#cart_count").text(response.cart_count);
                    $(".mini-cart-icon").click(); // Tải lại mini cart sau khi xóa sản phẩm
                }
            },
        });
    });

    /**********************************
     * * Page Cart
     * **************************/

    function updatCart(productId, quantity, $input) {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "/cart/update",
            type: "POST",
            data: {
                product_id: productId,
                quantity: quantity,
            },
            success: function (response) {
                // Backend returns `quantity`, `subTotal`, `total`, `grandTotal` (formatted strings)
                if (response.quantity !== undefined) {
                    $input.val(response.quantity);
                }
                if (response.cart_count !== undefined) {
                    $("#cart_count").text(response.cart_count);
                }
                if (response.subTotal !== undefined) {
                    $input
                        .closest("tr")
                        .find(".cart-product-subtotal")
                        .text(response.subTotal + " VNĐ");
                }
                if (response.total !== undefined) {
                    $(".cart-total").text(response.total + " VNĐ");
                }
                if (response.grandTotal !== undefined) {
                    $(".cart-grand-total").text(response.grandTotal + " VNĐ");
                }
            },
            error: function (xhr, status, error) {
                let msg = "Lỗi khi cập nhật giỏ hàng";
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    msg += ": " + xhr.responseJSON.error;
                } else {
                    msg += ": " + error;
                }
                alert(msg);
            },
        });
    }

    $(".remove-from-cart").on("click", function () {
        let productId = $(this).data("id");
        let row = $(this).closest("tr");
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "/cart/remove-cart",
            type: "POST",
            data: {
                product_id: $(this).data("id"),
            },
            success: function (response) {
                row.remove(); // Xóa dòng sản phẩm khỏi bảng
                if (response.cart_count !== undefined) {
                    $("#cart_count").text(response.cart_count);
                }
                $(".cart-total").text(response.total + " VNĐ"); // Cập nhật lại tổng tiền
                $(".cart-grand-total").text(response.grandTotal + " VNĐ"); // Cập nhật lại tổng tiền cuối cùng
                if ($(".cart-product-remove").length === 0) {
                    // Nếu không còn sản phẩm nào trong giỏ hàng, hiển thị thông báo
                    $("#cart-items").html(
                        "<p>Giỏ hàng của bạn đang trống.</p>",
                    );
                }
            },
            error: function (xhr, status, error) {
                alert("Lỗi khi xóa sản phẩm khỏi giỏ hàng: " + error);
            },
        });
    });

    /**********************************
     * * Checkout page
     * **************************/
    $("#list_address").change(function () {
        var addressId = $(this).val();
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "/checkout/get-address",
            type: "GET",
            data: { address_id: addressId },
            success: function (response) {
                if (response.success) {
                    $('input[name="full_name"]').val(
                        response.address.full_name,
                    );
                    $('input[name="phone"]').val(response.address.phone);
                    $('input[name="address"]').val(response.address.address);
                    $('input[name="city"]').val(response.address.city);
                    $('input[name="address_id"]').val(response.address.id);
                } else {
                    toastr.error(response.message, "Lỗi", {});
                }
            },
            error: function (xhr, status, error) {
                alert("Lỗi khi tải địa chỉ: " + error);
            },
        });
    });
    //Hanlde PayPal payment
    function togglePayment() {
        const warningMessage = $("#payment-method-warning");
        const orderButton = $("#order_button_cash");

        if (!warningMessage.length || !orderButton.length) {
            return;
        }

        const hasSelection =
            $('input[name="payment_method"]:checked').length > 0;

        warningMessage.toggleClass("d-none", hasSelection);
        orderButton.prop("disabled", !hasSelection);

        if ($("#payment_paypal").is(":checked")) {
            $("#paypal-button-container").show();
            $("#order_button_cash").hide();
        } else {
            $("#paypal-button-container").hide();
            $("#order_button_cash").show();
        }

        if (!hasSelection) {
            $("#paypal-button-container").hide();
            $("#order_button_cash").show();
            warningMessage.removeClass("d-none");
        }
    }
    togglePayment();
    $('input[name="payment_method"]').change(function () {
        togglePayment();
    });

    $(".ltn__checkout-payment-method form").on("submit", function (event) {
        if (!$('input[name="payment_method"]:checked').length) {
            event.preventDefault();
            toastr.warning(
                "Vui lòng chọn phương thức thanh toán.",
                "Thông báo",
            );
            $("#payment-method-warning").removeClass("d-none");
            $("#order_button_cash").prop("disabled", true);
        }
    });

    if ($("#paypal-button-container").length) {
        var totalPriceVnd = Number($(".totalPrice_Checkout").data("total-vnd"));
        if (!Number.isFinite(totalPriceVnd)) {
            totalPriceVnd = 0;
        }

        paypal
            .Buttons({
                createOrder: function (data, actions) {
                    return actions.order.create({
                        purchase_units: [
                            {
                                amount: {
                                    value: (totalPriceVnd / 23000).toFixed(2), // Tổng số tiền cần thanh toán
                                    currency_code: "USD", // Đơn vị tiền tệ
                                },
                            },
                        ],
                    });
                },
                onApprove: function (data, actions) {
                    return actions.order.capture().then(function (details) {
                        // Gửi thông tin đơn hàng về server để xử lý
                        fetch("/checkout/paypal", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                    "content",
                                ),
                            },
                            body: JSON.stringify({
                                orderID: data.orderID,
                                payerID: data.payerID,
                                transactionID: details.id,
                                amount: details.purchase_units[0].amount.value,
                                address_id: $("#list_address").val(),
                                paymentDetails: details,
                            }),
                        })
                            .then((response) => response.json())
                            .then((data) => {
                                if (data.success) {
                                    toastr.success(data.message, "Thành công");
                                    window.location.href = "/account"; // Chuyển hướng đến trang lịch sử đơn hàng
                                } else {
                                    toastr.error(data.message, "Lỗi", {});
                                }
                            });
                    });
                },
            })
            .render("#paypal-button-container");
    }

    /**********************************
     * * Handle Rating Product
     * **************************/
    if (window.location.pathname.startsWith("/product")) {
        let selectedRating = 0;

        //Handle hover on star rating
        $(".rating-star").hover(
            function () {
                let value = $(this).data("value");
                highlightStars(value);
            },
            function () {
                highlightStars(selectedRating);
            },
        );
        //Handle click on star rating
        $(".rating-star").click(function (e) {
            e.preventDefault();
            selectedRating = $(this).data("value");
            $("#rating-value").val(selectedRating);
            highlightStars(selectedRating);
        });

        function highlightStars(rating) {
            $(".rating-star i").each(function () {
                let starValue = $(this).parent().data("value");
                if (starValue <= rating) {
                    $(this).removeClass("far").addClass("fas"); //Hiển thị sao đầy
                } else {
                    $(this).removeClass("fas").addClass("far"); //Hiển thị sao rỗng
                }
            });
        }

        //Hanle submit rating with AJAX
        $("#review-form").submit(function (e) {
            e.preventDefault();

            let productId = $(this).data("product-id");
            let rating = $("#rating-value").val();
            let content = $("#review-content").val();

            if (!rating || rating === "0") {
                // Nếu chưa chọn sao, hiển thị cảnh báo đơn giản
                toastr.warning("Vui lòng chọn số sao đánh giá.", "Thông báo");
                return;
            }
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content",
                    ),
                },
            });
            $.ajax({
                url: "/review",
                type: "POST",
                data: {
                    product_id: productId,
                    rating: rating,
                    comment: content,
                },
                success: function (response) {
                    $("#review-content").val(""); // Clear nội dung đánh giá sau khi gửi thành công
                    highlightStars(0);
                    selectedRating = 0;
                    $(".ltn__comment-reply-area").hide();
                    toastr.success(response.message, "Thành công", {
                        timeOut: 5000,
                        extendedTimeOut: 2000,
                        closeButton: true,
                        progressBar: true,
                    });
                    loadReviews(productId);

                    // Tải lại phần đánh giá sau khi gửi thành công
                },
                error: function (xhr, status, error) {
                    alert("Lỗi khi gửi đánh giá: " + error);
                },
            });
        });

        function loadReviews(productId) {
            $.ajax({
                url: "/review/" + productId,
                type: "GET",
                success: function (response) {
                    $(".ltn__comment-inner").html(response);
                },
            });
        }
    }

    /**********************************
     * * Handle Page Contact
     * **************************/
    $("#contact-form").on("submit", function (e) {
        let name = $('input[name="name"]').val();
        let email = $('input[name="email"]').val();
        let phone = $('input[name="phone"]').val();
        let message = $('textarea[name="message"]').val();
        let errorsMessage = "";

        if (name.length < 3) {
            errorsMessage += "<li>Tên phải có ít nhất 3 ký tự.</li>";
        }
        if (phone.length < 10 || phone.length > 11) {
            errorsMessage +=
                "<li>Số điện thoại phải có từ 10 đến 11 ký tự.</li>";
        }
        let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            errorsMessage += "<li>Email khôngf hợp lệ.</li>";
        }
        if (errorsMessage !== "") {
            toastr.error(errorsMessage, "Lỗi gửi liên hệ");
            e.preventDefault();
        }
    });
    /**********************************
     * * Handle Wishlist
     * **************************/
    $(document).on("click", ".add-to-wishlist", function (e) {
        e.preventDefault();
        let productId = $(this).data("id");
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "/wishlist/add",
            type: "POST",
            data: {
                product_id: productId,
            },
            success: function (response) {
                if (response.status) {
                    $("#liton_wishlist_modal-" + productId).modal("show");
                }
            },
            error: function (xhr, status, error) {
                let message = "Có lỗi xảy ra";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                alert(message);
            },
        });
    });

    //Xóa sản phẩm khỏi wishlist
    $(document).on("click", ".wishlist-product-remove", function (e) {
        e.preventDefault();
        let productId = $(this).data("id");
        let row = $(this).closest("tr");
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "/wishlist/remove",
            type: "POST",
            data: {
                product_id: productId,
            },
            success: function (response) {
                if (response.status) {
                    row.remove();
                    toastr.success(
                        response.message,
                        "Đã xóa sản phẩm khỏi danh sách ưa thích",
                    );
                }
            },
            error: function (xhr, status, error) {
                alert("Lỗi khi xóa sản phẩm khỏi danh sách ưa thích: " + error);
            },
        });
    });

    /**********************************
     * * Handle Search Speech Recognition
     * **************************/
});
