<?php
class XMLParser extends DOMDocument implements Parser  {
    public function parse(Service $obj) {    
    	if($obj->serviceParams['service_type'] =='LOCATIONSEARCH')
    	{
    		$array_xml = $this->getLocations($obj->serviceParams['search_text'], $obj->serviceParams['search_type']);
    	}else if($obj->serviceParams['service_type'] =='LOCATIONCURRENT'){
       		$array_xml = $this->geCurrenttLocations($obj->serviceParams['service_url']);
    	}else{
    		$array_xml = $this->xml2array($obj->serviceParams['service_url']);
    	}
        return $array_xml;
    }
    
	/**
	 * 
	 * To generate the error message is some error is there.
	 * @param $message
	 * @return print an error message
	 */
	public function generateError($message='', $error_code='WN-003') {
		header('Content-type: text/xml');  
                $dom = new DOMDocument('1.0'); 
		$element = $dom->createElement('Error');
		$dom->appendChild($element);
		$messageEle = $dom->createElement('message', $message);
		$errorcode = $dom->createElement('errorcode', $error_code);
		$element->appendChild($errorcode);
		$element->appendChild($messageEle);
		echo $dom->saveXML();
		die();
    }
    
    /**
     * 
     * Create array from xml to PHP array...
     * @param $url
     * @param $get_attributes
     * @param $priority
     * @return array
     */
    function xml2array($url, $get_attributes = 1, $priority = 'tag') {
        $contents = "";
        if (!function_exists('xml_parser_create')) {
            return array();
        }
        $parser = xml_parser_create('');
        if (!($fp = @ fopen($url, 'rb'))) {
            return array();
        }
        while (!feof($fp)) {
            $contents .= fread($fp, 8192);
        }
        fclose($fp);
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents), $xml_values);
        xml_parser_free($parser);
        if (!$xml_values)
            return; //Hmm...
        $xml_array = array();
        $parents = array();
        $opened_tags = array();
        $arr = array();
        $current = & $xml_array;
        $repeated_tag_index = array();
        foreach ($xml_values as $data) {
        	unset($attributes, $value);
            extract($data);
            $result = array();

            $attributes_data = array();
            if (isset($value)) {
                if ($priority == 'tag')
                    $result = $value;
                else
                    $result['value'] = $value;
            }
            if (isset($attributes) and $get_attributes) {
                foreach ($attributes as $attr => $val) {
                    if ($priority == 'tag')
                        $attributes_data[$attr] = $val;
                    else
                        $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
                }
            }
            
            
            if ($type == "open") {
                $parent[$level - 1] = & $current;
                if (!is_array($current) or (!in_array($tag, array_keys($current)))) {
                    $current[$tag] = $result;
                    if ($attributes_data)
                        $current[$tag . '_attr'] = $attributes_data;
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    $current = & $current[$tag];
                }
                else {
                    if (isset($current[$tag][0])) {
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                        $repeated_tag_index[$tag . '_' . $level]++;
                    } else {
                        $current[$tag] = array(
                            $current[$tag],
                            $result
                        );
                        $repeated_tag_index[$tag . '_' . $level] = 2;
                        if (isset($current[$tag . '_attr'])) {
                            $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                            unset($current[$tag . '_attr']);
                        }
                    }
                    $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                    $current = & $current[$tag][$last_item_index];
                }
            } elseif ($type == "complete") {
            	if (!isset($current[$tag])) {
		            
            		$current[$tag] = $result;
            		// Code added 6-7-13
            		if(empty($current[$tag]) AND !isset($attributes)){
            			$current[$tag] = 0;
            		}
            		// End Code added 6-7-13
            		
            		$repeated_tag_index[$tag . '_' . $level] = 1;
                    if ($priority == 'tag' and $attributes_data)
                        $current[$tag . '_attr'] = $attributes_data;
                }
                else {
                    if (isset($current[$tag][0]) and is_array($current[$tag])) {
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                        if ($priority == 'tag' and $get_attributes and $attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                        }
                        $repeated_tag_index[$tag . '_' . $level]++;
                    } else {
                        $current[$tag] = array(
                            $current[$tag],
                            $result
                        );
                        $repeated_tag_index[$tag . '_' . $level] = 1;
                        if ($priority == 'tag' and $get_attributes) {
                            if (isset($current[$tag . '_attr'])) {
                                $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                                unset($current[$tag . '_attr']);
                            }
                            if ($attributes_data) {
                                $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                            }
                        }
                        $repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
                    }
                }
            } elseif ($type == 'close') {
                $current = & $parent[$level - 1];
            }
        }
        
        return ($xml_array);
    }

    /**
     * 
     * This is the final putput...
     * @param $array
     */
     public function printOutput($array){
     	$outputArray = array('root'=>$array);
     	header('Content-type: text/xml'); 
     	$this->dom = new XMLParser('1.0', 'utf-8');
		$this->dom->fromMixed($outputArray);
 		echo $this->dom->saveXML();
    } 
    
	
    /**
     * 
     * Enter description here ...
     * @param unknown_type $mixed
     * @param unknown_type $domElement
     */
 	public function fromMixed($mixed, DOMElement $domElement = null) {
        $domElement = is_null($domElement) ? $this : $domElement;
        if (is_array($mixed)) {
            foreach( $mixed as $index => $mixedElement ) {
                if ( is_int($index) ) {
                    if ( $index == 0 ) {
                        $node = $domElement;
                    } else {
                        $node = $this->createElement($domElement->tagName);
                        $domElement->parentNode->appendChild($node);
                    }
                } 
                 
                else {
                    $node = $this->createElement($index);
                    $domElement->appendChild($node);
                }
                 
                $this->fromMixed($mixedElement, $node);
            }
        } else {
            $domElement->appendChild($this->createTextNode($mixed));
        }
    }	
		
		
		
    function object2array($object) {
        $return = NULL;
        if (is_array($object)) {
            foreach ($object as $key => $value)
                $return[$key] = $this->object2array($value);
        } else {
            $var = get_object_vars($object);

            if ($var) {
                foreach ($var as $key => $value)
                    $return[$key] = ($key && !$value) ? NULL : $this->object2array($value);
            }
            else
                return $object;
        }
        return $return;
    }
    
    /**
     * @method: getLocations
     * 
     */
    function getLocations($search_text, $search_type=''){
		$objCrud = new crud;
		
		if($search_type=='ZIPCODE' or $search_type=='zipcode')
		{
			$query = "	
			SELECT *
			FROM zipcode_cities
			WHERE zip = '$search_text'
			LIMIT 1 ";
		}
		else
		{
			$query = "SELECT *
			FROM zipcode_cities
			WHERE city LIKE '$search_text%'
			GROUP BY city, state
			ORDER BY CASE WHEN `country` = 'US'
			THEN -1
			ELSE city
			END , city
			LIMIT 0 , 10 ";
		}
    	//echo $query;
		$locations = $objCrud->read($query);
		
		return $locations;
    }
    
    /**
     * 
     */
    public  function geCurrenttLocations($googleapi)
    {
  		$result = file_get_contents($googleapi);
		$json = json_decode($result);
	   	
		foreach ($json->results as $result)
		{
			$data =array();
			$city='';
			$state='';
			$country ='';	
			$postal_code = '';
			foreach($result->address_components as $addressPart) {
				if((in_array('locality', $addressPart->types)) && (in_array('political', $addressPart->types)))
		    		$city = $addressPart->long_name;
		    	else if((in_array('administrative_area_level_1', $addressPart->types)) && (in_array('political', $addressPart->types)))
		    		$state = $addressPart->short_name;
		    	else if((in_array('country', $addressPart->types)) && (in_array('political', $addressPart->types)))
		    		$country = $addressPart->short_name;
		    	else if((in_array('postal_code', $addressPart->types))){
		    		$postal_code = $addressPart->short_name;
		    	}	
			}
			
			$data['address'] =$result->formatted_address;
			$data['city']=$city;
			$data['state']=$state;
			$data['country']=$country;
			$data['latitude'] =$result->geometry->location->lat;
			$data['longitude'] =$result->geometry->location->lng;
			$data['zip'] =$postal_code;
			$results[]= $data;
			break;
		}
		return $results;
    }
}
