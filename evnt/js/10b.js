function go_apply() {

	var yn = confirm("응모하시겠습니까?");

	if (yn) {

		var answ = $("#answer").val();
		answ = answ.replace(/,/g, '');  // 금액표시의 , 를 없앰

		$.ajax({
			url  : "ajax_evnt_10b.php",
			type : "post",
			data : {answer : answ },
			dataType : "json",
			success : function (res) {
				alert(res["res"]);
				if (res["res"]=="SUCCESS") {
					alert("이벤트 응모가 성공적으로 이뤄졌습니다.");
				} else {
					alert("응모 실패");
				}
			},
			error : function (jqXHR, textStatus, errorThrown) {
				//console.log(jqXHR);
				alert(jqXHR+"\n"+textStatus+"\n"+errorThrown);
			}
		});
	}

}

function must_login() {
	var yn = confirm("로그인후 이용가능합니다.\n로그인 페이지로 이동하시겠습니까?");
	self.location.href="/bbs/login.php?url=https://www.hellofunding.co.kr/marketing/index.php";
}

function vaOpen_10b() {
	$('#vact_req_div').empty();
	if(confirm('가상계좌를 발급받으신후에 이용가능합니다.\n\n설레는 투자의 첫걸음인 개인 가상계좌를 발급 받으시겠습니까?\n\n' +
					 '──────────────────────────────\n\n' +
					 '[제3자에 의한 예치금 신탁관리 안내]\n' +
					 '신한은행에서 발급 및 관리하는 가상계좌를 통하여\n' +
					 '안전한 예치금 신탁관리를 받으실 수 있습니다.')) {

		$.ajax({
			url: '<?=$load_page?>',
			success: function(data) {
				$('#ajax_return_txt').val(data);
				$('#vact_req_div').html(data);
			}
		});
		$.blockUI({
			message: $('#vact_req_div'),
			css: { top:'<?=(G5_IS_MOBILE)?"1%":"10%"?>', left:'<?=(G5_IS_MOBILE)?"1%":"33%"?>', width:'<?=(G5_IS_MOBILE)?"98%":"605px"?>', height:'<?=(G5_IS_MOBILE)?"98%":""?>', border:0, cursor:'default' }
		});
	}
}