<?
if (!preg_match("/220\.117\.134/", $_SERVER["REMOTE_ADDR"])) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

$client_id = "n4ImG7KMJ0bHqzpO72l6";  // 헬로핀테크
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <title>간단한 지도 표시하기</title>
</head>
<body>
<? if($_REQUEST['type']=='1') { ?>
<div id="map" style="width:100%;height:400px;"></div>
<script type="text/javascript" src="https://openapi.map.naver.com/openapi/v3/maps.js?clientId=<?=$client_id?>"></script>
<script>
var mapOptions = {
    center: new naver.maps.LatLng(37.3595704, 127.105399),
    zoom: 10
};
var map = new naver.maps.Map('map', mapOptions);
</script>
<? } else { ?>
<div id="map" style="width:100%;height:400px;">
<?
$lat = "35.11035190";
$lng = "129.05653850";
$width = "640";
$height = "480";
$static_api_url = "https://openapi.naver.com/v1/map/staticmap.bin" .
                  "?clientId=" . $client_id .
                  "&url=http://hellofunding.co.kr/map_api_test.php" .
                  "&crs=EPSG:4326" .
                  "&exception=inimage" .
                  "&center={$lng},$lat" .
                  "&level=9" .
                  "&w={$width}&h={$height}" .
                  "&baselayer=default" .
                  "&format=png" .
                  "&markers={$lat},{$lng}";
?>
	<img src="<?=$static_api_url?>">
</div>
<? } ?>
</body>
</html>

<?
/*
?>
				<script type="text/javascript">
				var oPoint = new nhn.api.map.LatLng(<?=$product_row["lat"]?>, <?=$product_row["lng"]?>);
				var defaultLevel = 11;
				var oMap = new nhn.api.map.Map(document.getElementById('testMap'),
				           {
				             point : oPoint,
				             zoom : defaultLevel,
				             enableWheelZoom : true,
				             enableDragPan : true,
				             enableDblClickZoom : false,
				             mapMode : 0,
				             activateTrafficMap : false,
				             activateBicycleMap : false,
				             minMaxLevel : [ 1, 14 ],
				             size : new nhn.api.map.Size(588, 420)
				           });

				var oSize = new nhn.api.map.Size(28, 37);
				var oOffset = new nhn.api.map.Size(14, 37);
				var oIcon = new nhn.api.map.Icon('http://static.naver.com/maps2/icons/pin_spot2.png', oSize, oOffset);

				var oInfoWnd = new nhn.api.map.InfoWindow();
				oInfoWnd.setVisible(false);
				oMap.addOverlay(oInfoWnd);

				oInfoWnd.setPosition({ top:20, left:20 });

				// 마커 및 라벨
				var oMarker = new nhn.api.map.Marker(oIcon, { title : '<?=$product_row["title"]?>' });  //마커를 생성한다
				oMarker.setPoint(oPoint); //마커의 좌표를 oPoint 에 저장된 좌표로 지정한다
				oMap.addOverlay(oMarker); //마커를 네이버 지도위에 표시한다

				var oLabel = new nhn.api.map.MarkerLabel(); // - 마커 라벨 선언.
				oMap.addOverlay(oLabel); // - 마커 라벨 지도에 추가. 기본은 라벨이 보이지 않는 상태로 추가됨.
				oLabel.setVisible(true, oMarker); // 마커 라벨을 지도에 표시

				// 기본 마커 가시 상태 true | false
				oInfoWnd.attach('changeVisible', function(oCustomEvent) {
				  if (oCustomEvent.visible) {
				    oLabel.setVisible(true);
				  }
				});
				</script>
<?
*/
?>