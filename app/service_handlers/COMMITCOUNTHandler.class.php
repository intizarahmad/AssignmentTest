<?php
class COMMITCOUNTHandler implements ServiceHandler {
    function response(Service $obj, $response_array, Parser $outputParser) {
        if($obj->serviceParams['return_type'] == 'xml'){
            $this->XMLOutputHandler($obj, $response_array, $outputParser);
    	}else{
            $this->JSONOutputHandler($obj, $response_array, $outputParser);
        }
    }
    
    public function XMLOutputHandler(Service $obj, $array_xml, Parser $outputParser){
    	 	$outputParser->generateError($array_xml['Error']['message']);
    }
    
    public function JSONOutputHandler(Service $obj, $array, Parser $outputParser){
        print '<pre>';
		print_r($array);
        
    }
 
}
