$(document).ready(function() {

    // IE 접속 시 Edge로 전환
    if(window.navigator.userAgent.match(/MSIE|Internet Explorer|Trident/i) || navigator.userAgent.indexOf("Trident") > 0) {
        alert("헬로펀딩은 Chrome, Microsoft Edge 브라우저에 최적화 되어있습니다.\n" +
            "원활한 사용을 원하시면 Chrome, Microsoft Edge 브라우저를 권장합니다.\n확인버튼을 누르면 Edge브라우저로 이동됩니다.")
        window.location = 'microsoft-edge:' + window.location.href
    } else if(/MSIE \d |Trident.*rv:/.test(navigator.userAgent)) {
        alert("헬로펀딩은 Chrome, Microsoft Edge 브라우저에 최적화 되어있습니다.\n" +
            "원활한 사용을 원하시면 Chrome, Microsoft Edge 브라우저를 권장합니다.\n확인버튼을 누르면 Edge브라우저로 이동됩니다.")
        window.location = 'microsoft-edge:'+ window.location.href
    }

    // header
    var $window = $(window);
    var rollHeader = 30;
    var headerOffset = $('.header').offset().top;

    if(headerOffset != 0) {
        $('.header').addClass('roll');
    } else {
        $('.header').removeClass('roll');
    }

    // scroll
	$(window).scroll(function() {
	    var scroll = $window.scrollTop();
	    var windowTop = $window.scrollY;

		if( scroll >= rollHeader ) {
			$('.header').addClass('roll');
		} else {
		    $('.header').removeClass('roll');
		}
	});

    // 모바일 메뉴 클릭 시
    $(".m_header_wrap .menu_bar").on("click", function(e) {
        if ( $('.menu_list_container').hasClass('on') ){
            // close
            $('.menu_list_container').removeClass('on');
            $('.dimmed').fadeOut(200);
        } else {
            // open
            $('.menu_list_container').addClass('on');
            // dim
            $('.dimmed').fadeIn(200);
        }
    });

    // dim 클릭 시 모바일 메뉴 닫히기
    $(".m_header_wrap .dimmed").on("click", function(e) {
        if ( $('.menu_list_container').hasClass('on') ){
            // close
            $('.menu_list_container').removeClass('on');
            $('.dimmed').fadeOut(200);
        } else {
            // open
            $('.menu_list_container').addClass('on');
            // dim
            $('.dimmed').fadeIn(200);
        }
    });

    // 모바일 메뉴 depth1 클릭 시 depth2 메뉴 노출
    $('.m_header_wrap .depth1 > li').on('click', function() {
        if ( $(this).hasClass('active') ) {
            $(this).find(' > ul').stop().slideUp(300);
            $(this).removeClass('active');
            $(this).find('span').css('transform','rotate(0deg)');
        }
        else {
            $(this).find(' > ul').stop().slideDown(300);
            $(this).addClass('active');
            $(this).find('span').css('transform','rotate(90deg)');
        }
    });

    // 마우스 오버 시 depth2 보이기
    $('.header_wrap .depth1 > li').hover(function() {
        $(this).find('.depth2').css('display', 'block');
    }, function() {
        $(this).find('.depth2').css('display', 'none');
    });

    // aside
    $('.user_nick').on('click', function() {
        var info_content = $('.user_box');
        var close_button = info_content.find($('.close'));

        info_content.fadeToggle(200);

        close_button.on('click', function() {
            if( info_content.is(':visible') == true ) {
                info_content.fadeOut(200);
            }
        });
    });

    // 예치금 새로고침 flag
    if($("#reloadFlag").val() == 'Y') {
        $(".user_box").css('display', 'block');
        $('.close').on('click', function() {
            $('.user_box').fadeOut(200);
        });
    }

});

