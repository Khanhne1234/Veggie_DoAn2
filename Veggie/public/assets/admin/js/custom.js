$(document).ready(function () {
    /**********************************
  * *Management Users
  * **************************/

    $(document).on('click', '.upgradeStaff', function (e) {
        let button = $(this);
        let userId = button.data('userid');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '/admin/user/upgrade',
            type: 'POST',
            data: {
                user_id: userId
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    button.closest('.profile_view').find('.brief i').text('Staff');
                    button.closest('.profile_view').find('.changeStatus').hide();
                    button.hide();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                toastr.error('Đã có lỗi xảy ra');
            }
        });
    });
    $(document).on('click', '.changeStatus', function (e) {
        e.preventDefault();
        let button = $(this);
        let userId = button.data('userid');
        let status = button.data('status');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '/admin/user/updateStatus',
            type: 'POST',
            data: {
                user_id: userId,
                status: status
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    let action = button.data('action');

                    if (status == "banned") {
                        // Chặn thành công → hiển thị nút "Bỏ chặn"
                        button.replaceWith(
                            '<button type="button" class="btn btn-success btn-sm changeStatus" data-userid="' + userId + '" data-status="active" data-action="block">' +
                            '<i class="fa fa-user"> </i> Bỏ chặn</button>'
                        );
                    } else if (status == "deleted") {
                        // Xóa thành công → hiển thị nút "Khôi phục"
                        button.replaceWith(
                            '<button type="button" class="btn btn-success btn-sm changeStatus" data-userid="' + userId + '" data-status="active" data-action="delete">' +
                            '<i class="fa fa-user"> </i> Khôi phục</button>'
                        );
                    } else if (status == "active" && action == "block") {
                        // Bỏ chặn thành công → hiển thị nút "Chặn"
                        button.replaceWith(
                            '<button type="button" class="btn btn-warning btn-sm changeStatus" data-userid="' + userId + '" data-status="banned" data-action="block">' +
                            '<i class="fa fa-user"> </i> Chặn</button>'
                        );
                    } else if (status == "active" && action == "delete") {
                        // Khôi phục thành công → hiển thị nút "Xóa"
                        button.replaceWith(
                            '<button type="button" class="btn btn-danger btn-sm changeStatus" data-userid="' + userId + '" data-status="deleted" data-action="delete">' +
                            '<i class="fa fa-user"> </i> Xóa</button>'
                        );
                    }
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                toastr.error('Đã có lỗi xảy ra');
            }
        });
    });

    $('.btn_reset').on('click', function () {
        let form = $(this).closest('form');
        form.trigger('reset');
        form.find('input[type="file"]').val('');
        form.find('.image-preview').attr('src', '');
    });

    /**********************************
  * *Management Categories
  * **************************/
    $("#category-image").change(function () {
        let file = this.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $("#category-image-preview").attr("src", e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
    $('.category-image').change(function () {
        let file = this.files[0];
        let categoryId = $(this).data('id');
        if (file) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $(".image-preview").each(function () {
                    if ($(this).closest(".modal").attr("id") === "modalUpdate-" + categoryId) {
                        $(this).attr("src", e.target.result);
                    }
                })
            };
            reader.readAsDataURL(file);
        } else {
            $("#image-preview").attr("src", "");
        }
    });
    //Update Category
    $(document).on('click', '.btn-update-submit-category', function (e) {
        e.preventDefault();
        let button = $(this);
        let categoryId = $(this).data('id');
        let form = button.closest(".modal").find("form");
        let formData = new FormData(form[0]);
        formData.append("category_id", categoryId);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: `/admin/categories/update`,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                button.prop('disabled', true);
                button.text('Đang cập nhật...');
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    let categoryId = response.data.id;

                    //Regenerate new html for update row
                    let newRow = `
                    <tr id="category-row-${categoryId}">
                        <td>
                            <img src="${response.data.image}" alt="${response.data.name}" class="img-thumbnail" style="width: 80px; height: 60px; object-fit: cover;">
                        </td>
                        <td>${response.data.name}</td>
                        <td>${response.data.slug}</td>
                        <td>${response.data.description}</td>
                        <td>
                             <a class="btn btn-app btn-update-category" data-toggle="modal" data-target="#modalUpdate-${categoryId}">
                                <i class="fa fa-edit"></i>
                                Chỉnh sửa
                            </a>
                        </td>
                        <td>
                            <a class="btn btn-app btn-delete-category" data-id="${categoryId}">
                                <i class="fa fa-trash"></i>
                                Xóa
                            </a>
                        </td>
                    </tr>
                    `
                    //Replace old row with new row
                    $('#category-row-' + categoryId).replaceWith(newRow);
                    //Close modal update
                    $('#modalUpdate-' + categoryId).modal('hide');
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                toastr.error('Đã có lỗi xảy ra');
            },
            complete: function () {
                button.prop('disabled', false);
                button.html('<i class="fa fa-edit"></i> Chỉnh sửa');
            }
        });
    });
    //Delete Category
    $(document).on("click", ".btn-delete-category", function (e) {
        e.preventDefault();
        let button = $(this);
        let categoryId = button.data("id");
        let row = $('#category-row-' + categoryId);
        if (confirm("Bạn có chắc chắn muốn xóa danh mục này?")) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: `/admin/categories/delete`,
                type: 'POST',
                data: {
                    category_id: categoryId
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        row.fadeOut(500, function () {
                            $(this).remove();
                        });
                        // Xóa modal liên quan
                        $('#modalUpdate-' + categoryId).remove();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    toastr.error('Đã có lỗi xảy ra');
                }
            });
        }
    });
    /**********************************
  * *Management Products
  * **************************/
    $("#product-images").change(function () {
        let files = this.files;
        let previewContainer = $("#product-image-preview");
        previewContainer.empty();
        if (files.length > 0) {
            for (let i = 0; i < files.length; i++) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    previewContainer.append('<img src="' + e.target.result + '" class="product-image-preview" style="width: 100px; height: 100px; margin-right: 10px;">');
                };
                reader.readAsDataURL(files[i]);
            }
        } else {
            previewContainer.append('<p>Chưa có ảnh sản phẩm</p>');
        }
    });
    //Update Product
    $(".product-image").change(function (e) {
        let file = e.target.files;
        let productId = $(this).data("id");
        let previewContainer = $(".image-preview-container-" + productId);
        previewContainer.empty();
        if (file.length > 0) {
            for (let i = 0; i < file.length; i++) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    let img = $('<img/>').attr('src', e.target.result).addClass('product-image-preview').css({
                        'width': '100px',
                        'height': '100px',
                        'margin-right': '10px'
                    });
                    previewContainer.append(img);
                };
                reader.readAsDataURL(file[i]);
            }
        } else {
            previewContainer.append('<p>Chưa có ảnh sản phẩm</p>');
        }
    });
    $(document).on("click", ".btn-update-submit-product", function (e) {
        e.preventDefault();
        let button = $(this);
        let productId = button.data("id");
        let form = button.closest(".modal").find("form");
        let formData = new FormData(form[0]);
        formData.append("id", productId);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: `/admin/product/update`,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                button.prop('disabled', true);
                button.text('Đang cập nhật...');
            },
            success: function (response) {
                if (response.success) {
                    let product = response.data;
                    let productId = product.id;

                    let imageSrc = product.image_url ? product.image_url : (product.images && product.images.length > 0 ? product.images[0] : "/storage/uploads/products/default_product.webp");
                    //Regenerate new html for update row
                    let newRow = `
                    <tr id="product-row-${productId}">
                        <td>
                            <img src="${imageSrc}" alt="${product.name}" class="image-product" style="width: 100px;">
                        </td>
                        <td>${product.name}</td>
                        <td>${product.category_name}</td>
                        <td>${product.slug}</td>
                        <td>${product.description}</td>
                        <td>${product.stock}</td>
                        <td>${Number(product.price).toLocaleString('vi-VN')}VNĐ</td>
                        <td>${product.unit}</td>
                        <td>${product.status}</td>
                        <td>
                             <a class="btn btn-app btn-update-product" data-toggle="modal" data-target="#modalUpdate-${productId}">
                                <i class="fa fa-edit"></i>
                                Chỉnh sửa
                            </a>
                        </td>
                        <td>
                            <a class="btn btn-app btn-delete-product" data-id="${productId}">
                                <i class="fa fa-trash"></i>
                                Xóa
                            </a>
                        </td>
                    </tr>
                    `
                    //Replace old row with new row
                    $('#product-row-' + productId).replaceWith(newRow);
                    //Close modal update
                    $('#modalUpdate-' + productId).modal('hide');
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let key in errors) {
                        errorMessages.push(errors[key][0]);
                    }
                    toastr.error(errorMessages.join('<br>'));
                } else {
                    toastr.error('Đã có lỗi xảy ra');
                }
            },
            complete: function () {
                button.prop('disabled', false);
                button.html('<i class="fa fa-edit"></i> Chỉnh sửa');
            }
        });
    });
    //Delete Product
    $(document).on("click", ".btn-delete-product", function (e) {
        e.preventDefault();
        let button = $(this);
        let productId = button.data("id");
        if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này?")) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: `/admin/product/delete`,
                type: 'POST',
                data: {
                    product_id: productId
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#product-row-' + productId).remove();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    toastr.error('Đã có lỗi xảy ra');
                }
            });
        }
    });
    /**********************************
    * *Management Orders
    * **************************/
    $(document).on("click", ".confirm-order", function (e) {
        e.preventDefault();
        let button = $(this);
        let orderId = button.data("id");
        if (confirm("Bạn có chắc chắn muốn xác nhận đơn hàng này?")) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: `/admin/order/confirm`,
                type: 'POST',
                data: {
                    id: orderId
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        button.closest("tr").find(".status-order").html(`<span class=" custom-badge badge badge-warning">Đang vận chuyển</span>`);
                        button.hide();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    toastr.error('Đã có lỗi xảy ra');
                }
            });
        }
    });
    //Send Mail to customer
    $(document).on("click", ".send-invoice-mail", function (e) {
        e.preventDefault();
        let button = $(this);
        let orderId = button.data("id");
        if (confirm("Bạn có chắc chắn muốn gửi email này?")) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: `/admin/order-detail/send-invoice`,
                type: 'POST',
                data: {
                    id: orderId
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        button.closest("tr").find(".status-order").html(`<span class=" custom-badge badge badge-warning">Đang vận chuyển</span>`);
                        button.hide();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    toastr.error('Đã có lỗi xảy ra');
                }
            });
        }
    });
    //Cancel Order
    $(document).on("click", ".cancel-order", function (e) {
        e.preventDefault();
        let button = $(this);
        let orderId = button.data("id");
        if (confirm("Bạn có chắc chắn muốn hủy đơn hàng này?")) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: `/admin/order-detail/cancel-order`,
                type: 'POST',
                data: {
                    id: orderId
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        button.remove();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    toastr.error('Đã có lỗi xảy ra');
                }
            });
        }
    });

    /**********************************
    * *Management Contacts
    * **************************/
    // Tắt cảnh báo version check của CKEditor
    CKEDITOR.config.versionCheck = false;
    // Ghi đè handler compose từ custom.min.js để dùng callback slideToggle
    $('#compose, .compose-close').off('click').on('click', function () {
        $('.compose').slideToggle(400, function () {
            // Callback chạy SAU animation hoàn tất
            if ($('.compose').is(':visible') && !CKEDITOR.instances['editor-contact']) {
                CKEDITOR.replace('editor-contact');
            }
        });
    });
    $(document).on("click", ".contact-item", function (e) {
        //Get contact data form click item
        let contacName = $(this).data("name");
        let contactEmail = $(this).data("email");
        let contactMessage = $(this).data("message");
        let contactId = $(this).data("id");
        let contactIsReplied = $(this).data("is_replied");

        $(".mail_view .inbox-body .sender-info strong").text(contacName);
        $(".mail_view .inbox-body .sender-info span").text("<" + contactEmail + ">");
        $(".mail_view .view-mail p").text(contactMessage);

        $(".mail_view").show();
        console.log(contactIsReplied);
        if (contactIsReplied != 0) {
            $("#compose").hide();
        } else {
            //Add atribute data-email to button reply
            $(".send-reply-contact").attr("data-email", contactEmail);
            $(".send-reply-contact").attr("data-id", contactId);
            $("#compose").show();
        }
    });
    $(document).on("click", ".send-reply-contact", function (e) {
        e.preventDefault();
        let button = $(this);
        let contactEmail = button.data("email");
        let contactId = button.data("id");
        let editorInstance = CKEDITOR.instances['editor-contact'];
        if (!editorInstance) {
            toastr.error('Editor chưa sẵn sàng, vui lòng thử lại');
            return;
        }
        let message = editorInstance.getData();
        if (!message) {
            toastr.error('Vui lòng nhập nội dung phản hồi');
            return;
        }
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: `/admin/contact/reply`,
            type: 'POST',
            data: {
                contact_id: contactId,
                email: contactEmail,
                message: message
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    $(".mail_view").hide();
                    $("#compose").hide();
                    CKEDITOR.instances['editor-contact'].setData('');
                    $("#editor-contact").empty();

                    let contactItem = $(".contact-item[data-id='" + contactId + "']");
                    contactItem.attr("data-is_replied", 1);
                    contactItem.find("i.fa-circle").css("color", "green");


                    $(".compose").slideToggle();
                    button.removeAttr("data-email");
                    button.removeAttr("data-id");
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                toastr.error('Đã có lỗi xảy ra');
            }
        });
    });
    /**********************************
    * *Management Profile
    * **************************/
    $(".form-change-password").on("click", function (e) {
        e.preventDefault();
        $("#change-password").toggle();
        if ($("#change-password").is(":visible")) {
            $(this).text("Đóng");
        } else {
            $(this).text("Đổi mật khẩu");
        }
    })
    //Thay đổi avarta
    $(".update-avatar").on("click", function (e) {
        e.preventDefault();
        $("#avatar").trigger("click");
    })
    $("#avatar").on("change", function (e) {
        let file = e.target.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $(".avatar-view").attr("src", e.target.result);
            }
            reader.readAsDataURL(file);
            //Create formData to send image
            let formData = new FormData();
            formData.append('type', 'avatar');
            formData.append('avatar', file);
            updateProfile(formData);
        } else {
            $(".avatar-view").attr("src", " ");
        }
    });
    $("#update-profile").on("submit", function (e) {
        e.preventDefault();
        let valid = true;
        let name = $("#name").val();
        let email = $("#email").val();
        let phone = $("#phone").val();
        let address = $("#address").val();
        let avatar = $("#avatar").val();
        if (name.length < 3) {
            toastr.error('Vui lòng nhập tên có ít nhất 3 ký tự');
            valid = false;
        }
        let phoneRegex = /^\d{10}$/;
        if (!phoneRegex.test(phone)) {
            toastr.error('Vui lòng nhập số điện thoại có ít nhất 10 ký tự');
            valid = false;
        }
        if (address === "") {
            toastr.error('Vui lòng nhập địa chỉ');
            valid = false;
        }
        if (valid) {
            let formData = new FormData();
            formData.append('type', "profile")
            formData.append('name', name);
            formData.append('email', email);
            formData.append('phone', phone);
            formData.append('address', address);
            formData.append('avatar', avatar);
            updateProfile(formData);

        }
    });
    $("#change-password").on("submit", function (e) {
        e.preventDefault();
        let valid = true;
        let oldPassword = $("#old-password").val();
        let newPassword = $("#new-password").val();
        let confirmPassword = $("#confirm-password").val();
        if (oldPassword === "") {
            toastr.error('Vui lòng nhập mật khẩu cũ');
            valid = false;
        }
        if (newPassword.length < 6) {
            toastr.error('Vui lòng nhập mật khẩu mới có ít nhất 6 ký tự');
            valid = false;
        }
        if (confirmPassword != newPassword) {
            toastr.error('Vui lòng nhập mật khẩu xác nhận không khớp');
            valid = false;
        }
        if (valid) {
            let formData = new FormData();
            formData.append('type', "password")
            formData.append('old_password', oldPassword);
            formData.append('new_password', newPassword);
            formData.append('password_confirmation', confirmPassword);
            updateProfile(formData);

        }
    });
    function updateProfile(formData) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: `/admin/profile/update`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    let type = formData.get('type');
                    if (type === "profile") {
                        $("#name").val(formData.get('name'));
                        $("#email").val(formData.get('email'));
                        $("#phone").val(formData.get('phone'));
                        $("#address").val(formData.get('address'));
                        $("#user-address").text(formData.get('address'));
                        $("#user-email").text(formData.get('email'));
                        $("#user-phone").text(formData.get('phone'));
                    }
                    if (type === "password") {
                        $("#change-password")[0].reset();
                    }
                    if (type === "avatar" && response.data) {
                        $("#avatar-preview").attr("src", response.data.avatar);
                    }
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                toastr.error('Đã có lỗi xảy ra');
            }
        });
    }
    /**********************************
* *Manage Notification
* **************************/
    $(document).on('click', '.notification-item', function (e) {
        let noti_id = $(this).data('id');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: `/admin/notification/update`,
            type: 'POST',
            dataType: 'JSON',
            data: {
                id: noti_id
            },
            success: function (response) {
            },
            error: function (xhr, status, error) {
                alert("Đã xảy ra lỗi, vui lòng kiểm tra lại")
            }
        });
    });
});
