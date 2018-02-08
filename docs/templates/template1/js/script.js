$(document).ready(function() {
	
	$('.reviews').bxSlider({
		slideWidth		: 809,
		slideHeight		: 295,
		minSlides		: 1,
		maxSlides		: 1,
		auto			: false,
		mode			: 'fade'
	});

	$('.partners').bxSlider({
		slideWidth		: 200,
		slideHeight		: 140,
		minSlides		: 4,
		maxSlides		: 4,
		moveSlides		: 1,
		slideMargin		: 15,
		pager: false
	});

	$('.hits ul').bxSlider({
		slideWidth		: 250,
		//slideHeight		: 401,
		slideMargin		: 0,
		minSlides		: 1,
		maxSlides		: 2,
		moveSlides		: 1,
		pager: false
	});
	$('.mini_hits ul').bxSlider({
		slideWidth		: 180,
		//slideHeight		: 401,
		slideMargin		: 0,
		minSlides		: 1,
		maxSlides		: 3,
		moveSlides		: 1,
		pager: false
	});

	$(".formUp").fancybox({
		padding		: [30, 40, 30, 40],
		width		: 600,
		height		: 400,
		openEffect	: 'none',
		closeEffect	: 'none',
		helpers: {
			overlay: {
				locked: false
			}
		}
	});

	$(".prewImgs li a").on('click', function(e){
		e.preventDefault();
		if(!$(this).closest('li').hasClass("active")){
			var imgId = $(this).attr('rel');
			$(".bigImgs li").fadeOut("slow");
			$(".prewImgs li").removeClass("active");
			$(this).closest('li').addClass('active');
			$('.bigImgs').find('.itemImg'+imgId).delay(500).fadeIn("slow");
		}
	})
  
});