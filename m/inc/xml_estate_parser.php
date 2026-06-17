<?
	function XmlDecode($strVal,$strKind,$strRedeval)
	{
//		$xml = file_get_contents("test2.xml");         // 파싱할 대상XML 가져오기

		$parser = new XMLParser($strVal);             // 객체생성 parser라는 객체를 생성함
		$parser->Parse();     

		$XmLObjectName = "item";

		$XmLObjectCount = COUNT($parser->document->{$XmLObjectName});

		$x_coordinates = $parser->document->item[0]->point[0]->x[0]->tagData;
		$y_coordinates = $parser->document->item[0]->point[0]->y[0]->tagData;

		$total_coordinates = $x_coordinates."|".$y_coordinates;

		RETURN $total_coordinates;
	}
?>
