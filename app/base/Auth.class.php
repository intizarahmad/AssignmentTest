<?php
/**
 * @class Auth
 * 
 * 
 */
class Auth{
	private $time_interval = 60; // This is in Seconds

	// Temporary Array for storing cleint id information 
	private $client_secret_array = array(
			'sonyvita'              => array(
				'secret_key'            =>'ea3519cb9173ab9cda1f182ffcc3eb16',
				'secret_encription'     =>'0',
				'service_name'=>'BARON',
				),

                            'wnweb'         => array(
				'secret_key'            =>'meiwxqzeds1xkb4gopeeqrgucopplbno',
				'secret_encription'     =>'0',
				'service_name'=>'BARON',
				),

			'xbox'         => array(
				'secret_key'            =>'240ade23f6f7195125fa3ec35f2556fe',
				'secret_encription'     =>'0',
				'service_name'=>'BARON',
				),

			'roku'         => array(
				'secret_key'            =>'rrrrrrmeiwxqzeds1xkb4rgucopplbno',
				'secret_encription'     =>'0',
				'service_name'=>'BARON'
				),
                          
                         'baron'  => array(
				'secret_key'            =>'rrrrrrmeiwxqzeds1xkb4rgucopplbno',
				'secret_encription'     =>'0',
				'service_name'=>'BARON'
				),       
			);
		
	/**
	 * Error Code if the required parameter missing
	 * @var unknown_type
	 */
	public $parameter_missing = 'WN-001';
	
	/**
	 * Error Code if the authentication failed.
	 * @var unknown_type
	 */
	public $authentication_failed = 'WN-002';
	
	/**
	 * if the error comes from WDT
	 */
	public $wdt_error = 'WN-003';
	
	/**
	 * 
	 * Refrence of Logger Class to print log.
	 * @var Logger
	 */
	private $logger;
	
	/**
	 * 
	 * Enter description here ...
	 * @param $logger
	 */
	function __construct(Logger $logger) {  
		$this->logger = $logger;          
    }
    
    /**
     * @method Client Authentication
     * @return true if success / generate error
     */
    public function clientAuthentication($timeStamp='', $hashCode='', $client_id= ''){
		
    	// Stored the client IDs into an temp array.
   		$stored_client_ids = array_keys($this->client_secret_array);
		
   		// Serach the current id if is this in the temp array.
   		if(in_array($client_id, $stored_client_ids))
		{
			// Check if the the secret_encription is ON for more secure system for this client ID
			if($this->client_secret_array[$client_id]['secret_encription']=='1')
			{
				// Code to check if the requird parameters are empty
				if(empty($timeStamp)){
					$this->generateError("Required parameter 'TS' missing.", $this->parameter_missing);
				}
				if(empty($hashCode)){
					$this->generateError("Required parameter 'KEY' missing.", $this->parameter_missing);
				}
				
				
				// Code to check if the request is not too OLD
				$currenTime = time();
				$timeDifference = abs($currenTime-$timeStamp);
		    	if($timeDifference > $this->time_interval )
		    	{
		    		$this->generateError('Authentication failed: Time Stamp Expired.', $this->authentication_failed);
		    	}
				
		    	// make md5 of timeStamp and use secret key as salt. 
		    	if($hashCode == md5($this->client_secret_array[$client_id]['secret_key'].$timeStamp))
	    		{
	    			return true;
	    		}
	    		else
	    		{
	    			$this->generateError('Authentication failed.', $this->authentication_failed);
	    		}
	    			
			}else
			{
				return true;
			}
		}else{
			$this->generateError('This client id does not exist.', $this->authentication_failed);	
		}
   }
   
/**
 * For generating the Error in the form of XML
 * @method generateError()
 * @return true or generate Error
 * @param String $messge
 * @param Integer $error_code
 */
	public function generateError($messge='There is some error in processing.', $error_code='0'){
    	$dom = new DOMDocument('1.0');
		$element = $dom->createElement('Error');
		$dom->appendChild($element);
		$message = $dom->createElement('message', $messge);
		$errorcode = $dom->createElement('errorcode', $error_code);
		$element->appendChild($errorcode);
		$element->appendChild($message);
		echo $dom->saveXML();
		exit;
	}
	
	
    /**
     * @method app authebtication for iphone/andriod
     * @return true if success / generate error
     */
    public function appAuthentication($outputParser, $timeStamp='', $hashCode='', $client_id= ''){
		// Stored the client IDs into an temp array.
   		$stored_client_ids = array_keys($this->client_secret_array);
		
   		// Serach the current id if is this in the temp array.
   		if(in_array($client_id, $stored_client_ids))
		{
			// Check if the the secret_encription is ON for more secure system for this client ID
			if($this->client_secret_array[$client_id]['secret_encription']=='1')
			{
				// Code to check if the requird parameters are empty
				if(empty($timeStamp)){
					$outputParser->generateError("Required parameter 'TS' missing.", $this->parameter_missing);
				}
				if(empty($hashCode)){
					$outputParser->generateError("Required parameter 'KEY' missing.", $this->parameter_missing);
				}
				
				
				// Code to check if the request is not too OLD
				$currenTime = time();
				$timeDifference = abs($currenTime-$timeStamp);
		    	if($timeDifference > $this->time_interval )
		    	{
		    		$outputParser->generateError('Authentication failed: Time Stamp Expired.', $this->authentication_failed);
		    	}
				
		    	// make md5 of timeStamp and use secret key as salt. 
		    	if($hashCode == md5($this->client_secret_array[$client_id]['secret_key'].$timeStamp))
	    		{
	    			return true;
	    		}
	    		else
	    		{
	    			$outputParser->generateError('Authentication failed.', $this->authentication_failed);
	    		}
	    	}else
			{
				return true;
			}
		}else{
			$outputParser->generateError('This client id does not exist.', $this->authentication_failed);	
		}
   }
   
   
   public function getServiceName($client_key, $outputParser){
   		$stored_client_ids = array_keys($this->client_secret_array);
   		if(in_array($client_key, $stored_client_ids)){
   			$service_name = $this->client_secret_array[$client_key]['service_name'];
			if(empty($service_name)){
				$service_name = 'HAM';
			}	
   			return $service_name;
   		}else{
   			$outputParser->generateError('This client id does not exist.', $this->authentication_failed);	
   		}
   }
   
   /**
    * 
    * @param type $string_to_sign
    * @param type $secret
    * @return type
    */
   public function getSing($string_to_sign, $secret){
       return strtr(base64_encode(hash_hmac("sha1",
        utf8_encode($string_to_sign),
        utf8_encode($secret), true)),
        "+/", "-_");
   }
   
   /**
    * 
    * @param type $url
    * @param type $key
    * @param type $secret
    * @return type
    */
    public  function getSignRequest($url, $key, $secret) {
        $ts = time();
        $sig = $this->getSing($key.":".$ts, $secret);
        $q = strpos($url, "?") === false ? "?" : "&";
        $url .= sprintf("%ssig=%s&ts=%s", $q, $sig, $ts);
        return $url;
    }
   
}

