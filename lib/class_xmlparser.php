<?

class XMLParser {
	var $filename;
	var $xml;
	var $data;

	function XMLParser($xml_file) {
		$this->filename = $xml_file;
		$this->xml = xml_parser_create();
		xml_set_object($this->xml, $this);
		xml_set_element_handler($this->xml, 'startHandler', 'endHandler');
		xml_set_character_data_handler($this->xml, 'dataHandler');
		$this->parse($xml_file);
	}

	function parse($xml_file) {
		$fp = @fopen($xml_file, 'r');
		if($fp) {
			while(!feof($fp)) {
				$data = fgets($fp);
				$parse = xml_parse($this->xml, $data, feof($fp));
				if (!$parse) {
				  die(sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code($this->xml)), xml_get_current_line_number($this->xml)));
					xml_parser_free($this->xml);
				}
			}
			/*
			$bytes_to_parse = 1024;
			while ($data = fread($fp, $bytes_to_parse)) {
				$parse = xml_parse($this->xml, $data, feof($fp));
				if (!$parse) {
				  die(sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code($this->xml)), xml_get_current_line_number($this->xml)));
					xml_parser_free($this->xml);
				}
			}
			*/
			@fclose($fp);
			return true;
		}
		else {
			die('Cannot open XML data file: '.$xml_file);
			return false;
		}
	}


	function startHandler($parser, $name, $attributes) {
		$data['name'] = $name;
		if ($attributes) { $data['attributes'] = $attributes; }
		$this->data[] = $data;
	}

	function dataHandler($parser, $data) {
		if ($data = trim($data)) {
			$index = count($this->data) - 1;
			$this->data[$index]['data'] .= $data;
		}
	}

	function endHandler($parser, $name) {
		if (count($this->data) > 1) {
			$data = array_pop($this->data);
			$index = count($this->data) - 1;
			$this->data[$index]['child'][] = $data;
		}
		else if (count($this->data) == 1) {
			$this->data = array_pop($this->data);
		}
	}

}

?>