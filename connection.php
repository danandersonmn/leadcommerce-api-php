<?php
/**
 * Class to instantiate different api connections
 * 
 * @author Lead Commerce <support@leadcommerce.com>
 */
class connection
{

	static public $_path;
	static private $_identifier;
	static private $_key;
	static private $_headers;
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
                $match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', trim($match[1]));
                if( isset($retVal[$match[1]]) ) {
                    $retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
                } else {
                    $retVal[$match[1]] = trim($match[2]);
                }
            }
        }
        if ($retVal['LC-Limit-Remaining'] <= 100) {
        	sleep(60);
        }
    }

// ***********************************************************************//
// 
// ** error
// **
// ** Make data dump in case of error
// **
// ** @param 		$body, $url, $json, $type required for the class
// ** @return 		void

// ***********************************************************************//
    public function error($body, $url, $json, $type) {
    	global $error;
    	if (isset($json)) {
	    	$results = json_decode($body, true);
			$results = $results[0];
			$results['type'] = $type;
			$results['url'] = $url;
			$results['payload'] = $json;
			$error = $results;
		} else {
			$results = json_decode($body, true);
			$results = $results[0];
			$results['type'] = $type;
			$results['url'] = $url;
			$error = $results;
		}
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
		$microtime = (double)$itime[0].'.'.$iTime[1];
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

		$url = $this->_path.'/api/v2/'.$resource.".json";
        $this->setMicrotime();
		
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

		$url = $this->_path . '/api/v2/' .$resource."/create.json";
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

		$url = $this->_path . '/api/v2/' .$resource."/update.json";
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
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($curl, CURLOPT_HTTPGET, 1);
		curl_setopt($curl,CURLOPT_POSTFIELDS, $json);
		curl_setopt($curl,CURLOPT_POST, 1);  
		
		$response = curl_exec($curl);

		$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
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