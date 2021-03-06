<?php
/**
 * Class to instantiate different api connections
 * Comments Added By Dan
 *
 * Orignal Author
 * @author Lead Commerce <support@leadcommerce.com>
 *
 *
 *
 * //even though comments state that it will return or var_dump it will not var_dump
 * //when troubleshooting I highly recommend checking the values handled in the error function, as well as gathering the raw output
 * //from the curl requests.  Lead Commerce documentation can be somewhat unclear when it comes to working with some of the apis.
 * //the format of the json and required values may not be clearly described in the api docs. 
 *
 */

class connection
{

	public $_path;
	private $_identifier;
	private $_key;
	private $_headers;
	private $_microtime;


// ***********************************************************************//
// 
// ** __construct
// **
// ** Sets $_path, $_identifier, $_key, $_headers upon class instantiation
// **
// ** @param 		$path, $identifier, $key
// ** @return 		void
//
// ***********************************************************************//
	public function __construct($path, $identifier, $key) {
		$path = explode('/api/v2/', $path);
		$this->_path = $path[0];
		$this->_identifier = $identifier;
		$this->_key = $key;

		$encodedToken = base64_encode($this->_identifier.":".$this->_key);

		$authHeaderString = 'Authorization: Basic ' . $encodedToken;
		$this->_headers = array($authHeaderString, 'Accept: application/json');

	}	

// ***********************************************************************//
// 
// ** http_parse_headers
// **
// ** Read Output Headers and Requests Remaining
// **
// ** @param 		$headers
// ** @return 		void
//
// ***********************************************************************//
    public static function http_parse_headers( $header )
    {
        $retVal = array();
        $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));

        foreach( $fields as $field ) {
            if( preg_match('/([^:]+): (.+)/m', $field, $match) ) {
                $match[1] = preg_replace_callback('/(?<=^|[\x09\x20\x2D])./', function ($m){ return strtoupper($m[0]); }, trim($match[1]));
                if( isset($retVal[$match[1]]) ) {
                    $retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
                } else {
                    $retVal[$match[1]] = trim($match[2]);
                }
            }
        }
		if(isset($retVal['LC-Limit-Remaining'])) {
            if ($retVal['LC-Limit-Remaining'] <= 100) {
                sleep(60);
            }
        }
    }

// ***********************************************************************//
// 
// ** error
// ** TODO This error function does not handle all types of errors and can results in a undefined offset 0 php notice
	
// ** Make data dump in case of error
// **
// ** @param 		$body, $url, $json, $type required for the class
// ** @return 		void

// ***********************************************************************//
    public function error($body, $url, $json, $type) {
    	global $error;
		if($this->isJsonString($body) == false)
		{
			$results['type'] = $type;
			$results['url'] = $url;
			$results['payload'] = $json;
			$results['message'] = 'Unknown Error';
			$error = $results;
		}
		else
		{
			if (isset($json)) {
				$results = json_decode($body, true);
				$results = $results[0];
				$results['message'] = 'Catchable Error';
				$results['type'] = $type;
				$results['url'] = $url;
				$results['payload'] = $json;
				$error = $results;
			} else {
				$results = json_decode($body, true);
				$results = $results[0];  // May experience undefined offset: 0.  
				$results['message'] = 'Catchable Error';
				$results['type'] = $type;
				$results['url'] = $url;
				$error = $results;
			}
		}
    }
	
	
// ***********************************************************************//
// 
// ** isJsonString
// **
// ** Checks if the string is valid json
// **
// ** @return 		bool
//
// ***********************************************************************//
    private static function isJsonString($string)
	{
			// make sure provided input is of type string
		if (!is_string($string)) {
			return false;
		}
	
		// trim white spaces
		$string = trim($string);
	
		// get first character
		$firstChar = substr($string, 0, 1);
	
		// get last character
		$lastChar = substr($string, -1);
	
		// check if there is a first and last character
		if (!$firstChar || !$lastChar) {
			return false;
		}
	
		// make sure first character is either { or [
		if ($firstChar !== '{' && $firstChar !== '[') {
			return false;
		}
	
		// make sure last character is either } or ]
		if ($lastChar !== '}' && $lastChar !== ']') {
			return false;
		}
	
		// let's leave the rest to PHP.
		// try to decode string
		json_decode($string);
	
		// check if error occurred
		$isValid = json_last_error() === JSON_ERROR_NONE;
	
		return $isValid;
    }

// ***********************************************************************//
// 
// ** setMicrotime
// **
// ** Ensure api is meeting request requirements
// **
// ** @return 		void

// ***********************************************************************//
	private function setMicrotime()
	{
		$imicrotime = microtime(true);
		$iTime = explode ('.', $imicrotime);
		$microtime = (double)$iTime[0].'.'.$iTime[1];  //may experience undefined offset 1
		if($microtime <=($this->_microtime + 0.2) && $this->_microtime > 0)
		{
		    //echo "in setmicrotime";
			usleep(250000);	
		}
		else
		{
			$this->_microtime = $microtime;
		}
	}

// ***********************************************************************//
// 
// ** getList
// **
// ** Accepts the resource to perform the request on for LIST call
// **
// ** @param 		$resource string $resource a string to perform get on
// ** @return 		results or var_dump error
//
// ***********************************************************************//
	public function getList($resource,$json) {

		if ($resource == 'fulfillment')
		{
			$url = $this->_path.'/api/v2/orders/'.$resource.".json";
		}
		else if ($resource == 'redirect')
		{
			$url = $this->_path.'/api/v2/pages/'.$resource.".json";
		}
		else
		{
			$url = $this->_path.'/api/v2/'.$resource.".json";
		}
		
        //$this->setMicrotime();
		
		$results = self::curlQuery($url,$json);
		return $results;
	}
	

// ***********************************************************************//
// 
// ** getID
// **
// ** Accepts the resource to perform the request on for ID call
// **
// ** @param 		$resource string $resource a string to perform get on
// ** @return 		results or var_dump error
//
// ***********************************************************************//	
	public function getID($resource,$json) {

		$url = $this->_path . '/api/v2/' .$resource."/id.json";
		$this->setMicrotime();
		
		$results = self::curlQuery($url,$json);
		return $results;
	}


// ***********************************************************************//
// 
// ** getInfo
// **
// ** Accepts the resource to perform the request on for ID call
// **
// ** @param 		$resource string $resource a string to perform get on
// ** @return 		results or var_dump error
//
// ***********************************************************************//	
	public function getInfo($resource,$infolib,$json) {

		$url = $this->_path . '/api/v2/' .$resource."/".$infolib.".json";
		$this->setMicrotime();
		
		$results = self::curlQuery($url,$json);
		return $results;
	}
	
// ***********************************************************************//
// 
// ** create
// **
// ** Accepts the resource to perform the request on for ID call
// **
// ** @param 		$resource string $resource a string to perform get on
// ** @return 		results or var_dump error
//
// ***********************************************************************//	
	public function create($resource,$json) {
	
		if ($resource == 'create_receivement')
		{
			$url = $this->_path.'/api/v2/purchaseorders/receiving.json';
		}
		else if ($resource == 'create_redirect')
		{
			$url = $this->_path.'/api/v2/pages/create_redirect.json';
		}
		else if ($resource == 'create_comment')
		{
			$url = $this->_path.'/api/v2/supporttickets/create_comment.json';
		}
		else
		{
			$url = $this->_path . '/api/v2/' .$resource."/create.json";
		}
		$this->setMicrotime();
		
		$results = self::curlQuery($url,$json);
		return $results;
	}
	
// ***********************************************************************//
// 
// ** update
// **
// ** Accepts the resource to perform the request on for ID call
// **
// ** @param 		$resource string $resource a string to perform get on
// ** @return 		results or var_dump error
//
// ***********************************************************************//	
	public function update($resource,$json) {
	
		if ($resource == 'update_fulfillment')
		{
			$url = $this->_path.'/api/v2/orders/'.$resource.".json";
		}
		else
		{
			$url = $this->_path . '/api/v2/' .$resource."/update.json";
		}

		$this->setMicrotime();
		
		$results = self::curlQuery($url,$json);
		return $results;
	}
	
// ***********************************************************************//
// 
// ** adjustment
// **
// ** Accepts the resource to perform the request on for ID call
// **
// ** @param 		$resource string $resource a string to perform get on
// ** @return 		results or var_dump error
//
// ***********************************************************************//	
	public function adjustment($resource,$json) {

		$url = $this->_path . '/api/v2/' .$resource."/adjustment.json";
		$this->setMicrotime();
		
		$results = self::curlQuery($url,$json);
		return $results;
	}
	
// ***********************************************************************//
// 
// ** curlQuery
// **
// ** Performs a curl query to the instantiated class
// **
// ** @param 		$resource string $resource a string to perform get on
// ** @return 		results or var_dump error
//
// ***********************************************************************//	
	private function curlQuery($url,$data) {
		
		$json = http_build_query(array("data"=>json_encode($data)));

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->_headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_VERBOSE, 0);
		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($curl, CURLOPT_HTTPGET, 1);
		curl_setopt($curl,CURLOPT_POSTFIELDS, $json);
		curl_setopt($curl,CURLOPT_POST, 1);  
		
		$response = curl_exec($curl);

		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$headers = substr($response, 0, $header_size);
		$body = substr($response, $header_size);

		self::http_parse_headers($headers);
		
		$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ($http_status == 200) {
			$results = trim($body);
			$results = preg_replace('/\\\r\\\n|\\\r|\\\n\\\r|\\\n|\\\t/m', ' ', $results);
			return $results;
		} else {
			$this->error($body, $url, null, 'GET');
		}
		curl_close($curl);
	}
}
