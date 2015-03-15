<?php
define('DEVELOPMENT_ENVIRONMENT',true);
define('GITHUB_URL','https://api.github.com/repos/vmg/redcarpet/stats/contributors');


include_once 'app/config/core.php';

$service_type = 'COMMITCOUNT';
require_once 'app/service_handlers/'.$service_type.'Handler.class.php';	
$start_time = microtime(true);
$request_params = array_change_key_case($_REQUEST, CASE_UPPER);

$output = 'json';
$service = new Service;
$service->serviceParams = array( 
                                'return_type' => 'json',
                                'output' => $output,
                                'service_type' => $service_type
                               );
						

try {
	$outputParser = parser_Factory::getParser($output);
	$service->serviceParams['service_url'] = GITHUB_URL;    
	$request_handler = parser_Factory::getServiceHandler($service_type);
    
	$return_type = $service->serviceParams['return_type'];
	$parser = parser_Factory::getParser($return_type);
	$result_array = $service->parse($parser);
	
    echo $service->service_response($request_handler, $result_array, $outputParser);
	
}catch (Exception $e) {
    if(!isset($outputParser))
    {
    	die($e->getMessage());
    }

}
