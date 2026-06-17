	/* 로딩 및 스크롤 이벤트 발생 시 함수 호출 */
$(window).on('scroll load', function(e) {
	var nowScrollTop = $(window).scrollTop(),
		nowScrollLeft = $(window).scrollLeft();
		quickMenuSelect(nowScrollTop);
});
/* 스크롤 시 해당 콘텐츠의 퀵메뉴 활성화 */
var quickClickTF = false,
	quickOnClass = 'active';
var inViewCheck = function(el){
	var _this = el;
	var elementTop = _this.offset().top + ($(window).height() / 2);
	var elementBottom = elementTop + _this.outerHeight();
	var viewportTop = $(window).scrollTop();
	var viewportBottom = viewportTop + $(window).height();
	return elementBottom > viewportTop && elementTop < viewportBottom;
};
var quickMenuSelect = function(st){
	var scrollTop = st;
	var elQuick1stLi = '.sectlevel3 > li';
	if (quickClickTF) return;
	if (!scrollTop) {
		$(elQuick1stLi).removeClass(quickOnClass).eq(0).addClass(quickOnClass);
	} else {
		$(elQuick1stLi).each(function(i, el) {
			var _this = $(el);
			var elSections = _this.children('a').attr('href');
			if (inViewCheck($(elSections))) {
				$(elQuick1stLi).removeClass(quickOnClass);
				_this.addClass(quickOnClass);
			}
		});
	}
};
/* 퀵메뉴 클릭 시 해당 콘텐츠 이동 */
$('.tit2 li > a').on('click', function(e){
	e.preventDefault();
	var quickClickTime;
	var thisIndex = $(this).parent('li').index();
	var toGoTop = (thisIndex != 0) ? $($(this).attr('href')).offset().top - $('#header').outerHeight() : 0;
	$(this).parent('li').addClass(quickOnClass).siblings('li').removeClass(quickOnClass);
	$('html, body').animate({
		scrollTop: toGoTop
	}, 250);
	quickClickTF = true;
	clearTimeout(quickClickTime);
	quickClickTime = setTimeout(function(){
		quickClickTF = false
	}, 300);
});
