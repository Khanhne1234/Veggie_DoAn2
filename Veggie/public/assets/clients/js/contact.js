/* Contact Form Dynamic */

$(function() {

	// Get the form.
	var form = $('#contact-form');

	// Get the messages div.
	var formMessages = $('.form-messege');

	// Set up an event listener for the contact form.
	$(form).submit(function(e) {
		// Stop the browser from submitting the form.
		e.preventDefault();

		// Serialize the form data.
		var formData = $(form).serialize();

		// Submit the form using AJAX.
		$.ajax({
			type: 'POST',
			url: $(form).attr('action'),
			data: formData,
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		})
		.done(function(response) {
			// Make sure that the formMessages div has the 'success' class.
			$(formMessages).removeClass('error');
			$(formMessages).addClass('success');

			// Set the message text.
			$(formMessages).text(response);

			// Clear the form.
			$('#contact-form input,#contact-form textarea').val('');

			// Show toastr success so users see a popup notification
			if (window.toastr) {
				toastr.success(response, 'Thành công', {
					timeOut: 5000,
					extendedTimeOut: 2000,
					closeButton: true,
					progressBar: true,
				});
			}
		})
		.fail(function(data) {
			// Make sure that the formMessages div has the 'error' class.
			$(formMessages).removeClass('success');
			$(formMessages).addClass('error');

			// Set the message text.
			var errMsg = '';
			if (data.responseJSON && data.responseJSON.message) {
				errMsg = data.responseJSON.message;
			} else if (data.responseText !== '') {
				errMsg = data.responseText;
			} else {
				errMsg = 'Oops! An error occured and your message could not be sent.';
			}

			$(formMessages).text(errMsg);

			// Show toastr error as well
			if (window.toastr) {
				toastr.error(errMsg, 'Lỗi gửi liên hệ', {
					timeOut: 5000,
					extendedTimeOut: 2000,
					closeButton: true,
					progressBar: true,
				});
			}
		});
	});

});
