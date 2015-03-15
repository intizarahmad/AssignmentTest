<?php
class Service{    
    public $serviceParams;
    function __construct() {            
        
    }
     
    public function parse(Parser $parser) {
    	return $parser->parse($this);
    }
    
    /**
     * 
     * @method generateError ...
     * @param $parser
     * @param $message
     * @return call to parser error method
     */
	public function generateError(Parser $parser, $message='There is some exception in service.') {
    	return $parser->generateError($message);
    }
    
    /**
     * 
     * @param ServiceHandler $service_handler
     * @param type $parsed_array
     * @param Parser $outputParser
     * @return type
     */
    public function service_response(ServiceHandler $service_handler,$parsed_array, Parser $outputParser){
        return $service_handler->response($this,$parsed_array, $outputParser);
    }
     
}

