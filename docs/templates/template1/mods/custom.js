jQuery(document).ready(function ($) {
	$(".fofm").validate({
		focusInvalid: true,
		errorClass: "input_error",
		submitHandler: function (form) {
			var str = $(form).serialize();
			$.ajax({
				type: "POST",
				url: "/contact.php",
				data: str,
				success: function (msg) {
					if (msg == 'ok') {
						$('.cbok').css('display', 'block');
						$('.fofm').css('display', 'none');
						$(':input', '.fofm').not(':button, :submit, :reset, :hidden').val('').removeAttr('checked').removeAttr('selected');
						//$.fancybox.close();
					}
					else {
						$('.cbok').html('<p>Сообщение не отправлено, убедитесь в правильности заполнение полей.</p>');
						$('.fofm').css('display', 'block');
					}
				}
			});
			return false;
		}
	})
});

jQuery(document).ready(function ($){
	$(".fofm2").validate({
		focusInvalid: true,
		errorClass: "input_error",
		submitHandler: function (form) {
			var str = $(form).serialize();
			$.ajax({
				type: "POST",
				url: "/contact.php",
				data: str,
				success: function (msg) {
					if (msg == 'ok') {
						$('.cbok2').css('display', 'block');
						$('.fofm2').css('display', 'none');
						$(':input', '.fofm2').not(':button, :submit, :reset, :hidden').val('').removeAttr('checked').removeAttr('selected');
						//$.fancybox.close();
					}
					else {
						$('.cbok2').html('<p>Сообщение не отправлено, убедитесь в правильности заполнение полей.</p>');
						$('.fofm2').css('display', 'block');
					}
				}
			});
			return false;
		}
	})
});
