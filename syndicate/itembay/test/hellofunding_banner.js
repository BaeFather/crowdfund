
get_ban();

function get_ban() {
	
	if (window.jQuery) {
	} else {
	}
	
			$.ajax({
				type: "POST",
				url: "http://chosun.hellofunding.kr/test/get_new_prd.php",
				dataType: "json",
				cache: false,
				//data: {TID:<?=$TID;?>},
				success: function(res) {
					console.log(res);
					var html_str = "<div style='text-align:center;width:400px;height:200px;background:url(\"https://hellofunding.co.kr/data/product/3IMSbvxEmn\") center ;color:white;'>";
					html_str += "<div style='margin:auto auto;'>"+res.title+"</div>";
					html_str += "</div>";
					document.write(html_str);
				},
				error: function(request, status, error) { 
					alert("code: "+request.status+"\n"+"message: "+request.responseText+"\n"+"error: "+error);
				}
			});
}
