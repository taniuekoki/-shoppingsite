$(function(){

	let windowWidth = $(window).width();

	const windowSm = 640;
	
	if (windowWidth <= windowSm) {
		//横幅640px以下（スマホ）に適用させるJavaScriptを記述
		$('.topslider').slick({
			slidesToShow:1,//表示枚数
			slidesToScroll: 1,//スライド枚数
			autoplay: true,//自動再生ON
			autoplaySpeed: 5000,//切り替え時間
			fade: true, //フェードON
			speed: 2000,  //フェードスピード
			// dots: true,//ドット
			arrows: false,//矢印
		});
		
		
		$('.slider').slick({
			slidesToShow:3,
			slidesToScroll: 1,
			autoplay: true,
			autoplaySpeed: 3000,
			arrows: false,
			// centerPadding: "5%",
			// dots: true,
		});

		// $('.photoGarelly').css("grid-template-rows","(windowWidth * 0.33)px (windowWidth * 0.33)px (windowWidth * 0.33)px (windowWidth * 0.33)px")
		
		let pg = document.getElementsByClassName('photoGarally').height;
		console.log (pg);
		$('.slider_logo').css("height","pg");

		
	} else {

		//横幅640px以上（PC、タブレット）に適用させるJavaScriptを記述
		$('.topslider').slick({
			slidesToShow:1,//表示枚数
			slidesToScroll: 1,//スライド枚数
			autoplay: true,//自動再生ON
			autoplaySpeed: 5000,//切り替え時間
			fade: true, //フェードON
			speed: 2000,  //フェードスピード
			dots: true,//ドット
			arrows: false,//矢印
		});
		
		$('.slider').slick({
			slidesToShow:4,
			slidesToScroll: 1,
			autoplay: true,
			autoplaySpeed: 2500,
			arrows: true,
			centerPadding: "15%",
			dots: true,
		});
		
	}







	
	

	
	
	
});
