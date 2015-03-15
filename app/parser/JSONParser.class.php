<?php
class JSONParser implements Parser {
    public function parse(Service $obj) {
        $array_xml = $this->json2array($obj->serviceParams['service_url']);
    	return $array_xml;
    }
    
    /*
     * function to get the the result from HAMweather Result 
     */
    public function json2array($url){
		
		$ch = curl_init();
		echo exec('curl -i "'.$url.'"');
		
        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       

        $json_data = curl_exec($ch);
        curl_close($ch);
		
        $json_array = json_decode($json_data, 1	);
        print '<pre>';
		print_r($json_array);
        return $json_array;
    }
     
    
    
	/**
	 * 
	 * To generate the error message is some error is there.
	 * @param $message
	 * @return print an error message
	 */
	public function generateError($message='', $errorcode='03') {
		$returnArray = array('success' => false, 'errorcode'=>$errorcode, 'error' =>$message );
                echo json_encode($returnArray);
                die();
        }
    
	public function printOutput($array) {
		echo json_encode($array); 
        }

}
