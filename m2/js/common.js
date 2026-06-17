
$(document).ready(function(){

	var winH = $(window).height();

	//왼쪽메뉴 열기
	$('.nav_open').click(function(){
		$('.navi').stop().animate({'left':0});
		$('.navi_bg').fadeIn('fast'); //배경 어둡게
		$('#wrap').css({height:winH,overflow:'hidden'}); //뒤에 스크롤 안되게
		//$('#container').css({position:'fixed'}); //뒤에 스크롤 안되게
	});

	//왼쪽메뉴 닫기
	$('.nav_close').click(function(){
		$('.navi').stop().animate({'left':'-260px'});
		$('.navi_bg').fadeOut('fast'); //배경 사라지게
		$('#wrap').css({height:'auto',overflow:'auto'}); //원복
		//$('#container').css({position:'relative'}); //원복
	});

	//배경 클릭시 왼쪽메뉴 닫힘
	$('.navi_bg').click(function(){
		$('.navi').animate({left:'-260px'});
		$('.navi_bg').fadeOut('fast'); //배경 사라지게
		$('#wrap').css({height:'auto',overflow:'auto'}); //원복
		//$('#container').css({position:'relative'}); //원복
	});
	//투뎁스 열기
	$('.navi li').click(function(){
		if($(this).hasClass('on')){
			$(this).removeClass('on').find('.subMenu').slideUp();
		}else{
			$(this).siblings().removeClass('on').find('.subMenu').slideUp();
			$(this).addClass('on').find('.subMenu').slideDown('fast');
		}
	});

});