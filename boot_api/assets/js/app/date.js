var arrWeek = ['일', '월', '화', '수', '목', '금', '토'];

function FormatDate(inDate, format) {
	var dateStr = "";

	var ad = inDate.toString().split(' ');

	var year = inDate.getFullYear();
	var month = (parseInt(inDate.getMonth()) + parseInt(1));
	var day = parseInt(ad[2]); // 이상하게 day가 오류인 관계로 getDay() 사용 대신
	var week = arrWeek[inDate.getDay()];
	var hour = inDate.getHours();
	var min = inDate.getMinutes();
	var sec = inDate.getSeconds();

	dateStr = year + "-";
	dateStr += month < 10 ? "0" + month : month;
	dateStr += "-";
	dateStr += day < 10 ? "0" + day : day;

	switch (format) {
		case "yyyy-MM-dd HH:mm:ss":
			dateStr += " ";
			dateStr += hour < 10 ? "0" + hour : hour;
			dateStr += ":";
			dateStr += min < 10 ? "0" + min : min;
			dateStr += ":";
			dateStr += sec < 10 ? "0" + sec : sec;
			break;
		case "yyyyMMdd":
			dateStr = ReplaceALL(dateStr, "-", "");
			break;
		case "yyyy.MM.dd":
			dateStr = ReplaceALL(dateStr, "-", ".");
			break;
		default:
			break;
	}
	// alert(inDate + "/" + month + "/" + day);
	return dateStr;
}