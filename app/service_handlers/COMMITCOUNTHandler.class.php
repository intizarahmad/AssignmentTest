<?php
/**
 * Class to handle the reponse from the GIT API
 */
class COMMITCOUNTHandler implements ServiceHandler {
    /**
     * 
     * @param Service $obj
     * @param type $response_array
     * @param Parser $outputParser
     */
    function response(Service $obj, $response_array, Parser $outputParser) {
        if($obj->serviceParams['return_type'] == 'json'){
			$this->JSONOutputHandler($obj, $response_array, $outputParser);
       }else{
		     $this->XMLOutputHandler($obj, $response_array, $outputParser);
    	}
    }
    
    /**
     * 
     * @param Service $obj
     * @param type $array_xml
     * @param Parser $outputParser
     */
    public function XMLOutputHandler(Service $obj, $array_xml, Parser $outputParser){
    	$outputParser->generateError('XML NOT SUPPORTED.');
    }
    
    /**
     * 
     * @param Service $obj
     * @param type $response_array
     * @param Parser $outputParser
     */
    public function JSONOutputHandler(Service $obj, $response_array, Parser $outputParser){
        if(isset($response_array['message'])){
            echo $response_array['message'];
        }else{
            $totalCommitCount = 0;
            foreach($response_array as $data){
                $totalCommitCount = $totalCommitCount+$data['total']; 
            }
            echo 'Total Commits:' .$totalCommitCount;
        }	
    }
}
