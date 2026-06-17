<?
include_once('./_common.php');
?>
<?
//print_r($_POST);

$ad = explode(' ', $addr);

$retn = array();

$retn["hs_dp"] = 0;

if ($ad[0]=="서울특별시" or $ad[0]=="서울시" or $ad[0]=="서울") {

	$retn["hs_dp"] = 50000000;

} else if ($ad[0]=="경기도" or $ad[0]=="경기") {

	if ($ad[1]=="의정부시" or $ad[1]=="구리시" or $ad[1]=="하남시" or $ad[1]=="고양시" or $ad[1]=="수원시"
	 or $ad[1]=="성남시"  or $ad[1]=="안양시" or $ad[1]=="부천시" or $ad[1]=="광명시" or $ad[1]=="과천시"
	 or $ad[1]=="의왕시"  or $ad[1]=="화성시" or $ad[1]=="김포시" or $ad[1]=="군포시" or $ad[1]=="시흥시"
	 or $ad[1]=="용인시") {

		$retn["hs_dp"] = 43000000;

	} else if ($ad[1]=="남양주시") {

		if ($ad[2]=="호평동" or $ad[2]=="평내동" or $ad[2]=="금곡동" or $ad[2]=="일패동" or $ad[2]=="이패동" 
		 or $ad[2]=="삼패동" or $ad[2]=="가운동" or $ad[2]=="수석동" or $ad[2]=="지금동" or $ad[2]=="도농동") {
			$retn["hs_dp"] = 43000000;
		} else {
			$retn["hs_dp"] = 20000000;
		}

	} else if ($ad[1]=="안산시" or $ad[1]=="광주시" or $ad[1]=="파주시" or $ad[1]=="이천시" or $ad[1]=="평택시") {

		$retn["hs_dp"] = 23000000;

	} else if ($ad[1]=="동두천시" or $ad[1]=="오산시" or $ad[1]=="양주시" or $ad[1]=="포천시" or $ad[1]=="여주시" or $ad[1]=="안성시" or $ad[1]=="연천군" or $ad[1]=="가평군" or $ad[1]=="양평군") {

		$retn["hs_dp"] = 20000000;

	} else {
		
	}
	
} else if ($ad[0]=="인천광역시" or $ad[0]=="인천시") {

	if ($ad[1]=="강화군" or $ad[1]=="옹진군") {

		$retn["hs_dp"] = 20000000;

	} else if ($ad[2]=="대곡동" or $ad[2]=="불로동" or $ad[2]=="마전동" or $ad[2]=="금곡동" or $ad[2]=="오류동" or $ad[2]=="왕길동" or $ad[2]=="당하동" or $ad[2]=="원당동") {

		$retn["hs_dp"] = 23000000;
	
	} else {

		$retn["hs_dp"] = 43000000;
	
	}

} else if ($ad[0]=="세종특별자치시" or $ad[0]=="세종시") {

	$retn["hs_dp"] = 43000000;

} else if ($ad[0]=="대전광역시" or $ad[0]=="대전시") {

	$retn["hs_dp"] = 23000000;

} else if ($ad[0]=="광주광역시" or $ad[0]=="광주시") {

	$retn["hs_dp"] = 23000000;

} else if ($ad[0]=="대구광역시" or $ad[0]=="대구시") {

	if ($ad[1]=="달성군") {

		$retn["hs_dp"] = 20000000;

	} else {

		$retn["hs_dp"] = 23000000;

	}

} else if ($ad[0]=="울산광역시" or $ad[0]=="울산시") {

	if ($ad[1]=="울주군") {

		$retn["hs_dp"] = 20000000;

	} else {

		$retn["hs_dp"] = 23000000;

	}

} else if ($ad[0]=="부산광역시" or $ad[0]=="부산시") {

	if ($ad[1]=="기장군") {

		$retn["hs_dp"] = 20000000;

	} else {

		$retn["hs_dp"] = 23000000;

	}

} else {
	$retn["hs_dp"] = 20000000;
}
echo json_encode($retn, JSON_UNESCAPED_SLASHES);
?>