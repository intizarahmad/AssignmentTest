<?php
define('DEVELOPMENT_ENVIRONMENT',true);


include_once 'app/config/core.php';

$service_type = 'COMMITCOUNT';
require_once 'app/service_handlers/'.$service_type.'Handler.class.php';	


// Get the input parameters
$request_params = array_change_key_case($_REQUEST, CASE_LOWER);
$username = isset($request_params['username'])?strtolower($request_params['username']):''; 
$repository = isset($request_params['repository'])?strtolower($request_params['repository']):''; 


// Return type JSON
$output = 'json';
$service = new Service;
$service->serviceParams = array( 
                                'return_type' => 'json',
                                'output' => $output,
                                'service_type' => $service_type
                               );
						

try {
	$outputParser = parser_Factory::getParser($output);
	
        // Check the required fields.
        if(empty($username)){
		$outputParser->generateError("Required parameter 'username' is missing.", '404');
	}
	
	if(empty($repository)){
		$outputParser->generateError("Required parameter 'repository' is missing.", '404');
	}
			
	$service->serviceParams['service_url'] = REPOSITORY_URL.$username.'/'.$repository.'/stats/commit_activity';;    
	$request_handler = parser_Factory::getServiceHandler($service_type);
    
	$return_type = $service->serviceParams['return_type'];
	$parser = parser_Factory::getParser($return_type);
	$result_array = $service->parse($parser);
	
        $service->service_response($request_handler, $result_array, $outputParser);
	
}catch (Exception $e) {
    if(!isset($outputParser))
    {
    	die($e->getMessage());
    }
}
