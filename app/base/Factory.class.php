<?php
class parser_Factory {
    public static function getParser($format) {
        // construct our class name and check its existence
        $class = strtoupper($format). 'Parser';
        
       	if(class_exists($class)) {
            // return a new Writer object
            return new $class();
        }
        // Otherwise we fail
 		throw new Exception('Unsupported format:'.$format);
    }
    
    public static function getServiceHandler($service_type){
        // construct our class name and check its existence
        $class = strtoupper($service_type).'Handler';
        if(class_exists($class)) {            
            return new $class();
        }
        // otherwise we fail
        throw new Exception('Unsupported Service, Missing Class '.strtoupper($service_type).'Handler. Create a file in '.'service_handlers/'.strtoupper($service_type).'Handler.class.php');
    }
    
    
    
}