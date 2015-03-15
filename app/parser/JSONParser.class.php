<?php
class JSONParser implements Parser {
    public function parse(Service $obj) {
        $data = $this->json2array($obj->serviceParams['service_url']);
    	return $data;
    }
    
    /*
     * function to get the the Result 
     */
    public function json2array($url){
	//echo $url;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);  
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);		

        $json_data = curl_exec($ch);
		curl_close($ch);
		
        $json_array = json_decode($json_data, 1	);
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
}
