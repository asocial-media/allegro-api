<?php
/**
 * Namespace declaration
 */
namespace Zoondo\AllegroApi;

/**
 * Object PHP interface for Allegro REST API
 * 
 * This class allows you to call any resource 
 * with correct request
 * 
 * It also implements some features which
 * will save you a lot of time
 * 
 * Example:
 * <pre>
 * // Register your application here:
 * // https://apps.developer.allegro.pl
 * 
 * // Creating auth URL using client id and redirect_uri
 * var_dump(
 *     AllegroRestApi::getAuthLink($clientId, $redirectUri)
 * );
 * 
 * // After clicking the link and granting permission you 
 * // will be redirected to $redirectUri with "code"
 * //
 * // Use given code to finally generate access token
 * $tokens = AllegroRestApi::generateToken(
 *     $_GET['code'], 
 *     $clientId, 
 *     $clientSecret, 
 *     $redirectUri
 * );
 * 
 * // Above token will be active for 12 hours and you can
 * // refresh it indefinitely (in example using cron)
 * AllegroRestApi::refreshToken(
 *     $tokens->refresh_token, 
 *     $clientId, 
 *     $clientSecret, 
 *     $redirectUri
 * );
 * 
 * // Creating an instance of RestApi
 * $restApi = new AllegroRestApi($tokens->access_token);
 * 
 * // Getting our comments
 * $response = $restApi->get('/sale/user-ratings?user.id=' . $yourUserId)
 * </pre>
 * 
 * @see        https://developer.allegro.pl/about/
 * @author     Maciej Strączkowski <m.straczkowski@gmail.com>
 * @copyright  ZOONDO.EU Maciej Strączkowski
 * @version    2.0.0
 */
class AllegroRestApi
{
    /**
     * An url address for production API
     */
    const URL = 'https://api.allegro.pl';
    
    /**
     * An url address for sandbox API
     */
    const SANDBOX_URL = 'https://api.allegro.pl.allegrosandbox.pl';
        
    /**
     * Allegro REST API access token
     * 
     * @var string
     */
    protected $token = null;
    
    /**
     * Should we use sandbox mode?
     * 
     * @var boolean
     */
    protected $sandbox = false;
        
    /**
     * Saves given token and sandbox boolean
     * value into class properties
     * 
     * @param   string   $token
     * @param   boolean  $sandbox
     */
    public function __construct($token, $sandbox = false)
    {
        $this->setToken($token);
        $this->setSandbox($sandbox);
    }
    
    /**
     * Returns an authorization link which user 
     * should click to give access
     * 
     * @param   string  $clientId
     * @param   string  $redirectUri
     * @return  string
     */
    public static function getAuthLink($clientId, $redirectUri)
    {
        return "https://allegro.pl/auth/oauth/authorize"
            . "?response_type=code"
            . "&client_id=$clientId"
            . "&redirect_uri=$redirectUri";
    }
    
    /**
     * Generates access token using given 
     * credentials and code
     * 
     * @param   string  $code        Code from allegro
     * @param   string  $clientId    Client ID
     * @param   string  $redirectUri Client secret
     * @return  object
     */
    public static function generateToken($code, $clientId, $clientSecret, $redirectUri)
    {
        // Creating an instance of class
        $api = new AllegroRestApi(null, null);
        
        // Returning response
        return $api->sendRequest("https://allegro.pl/auth/oauth/token"
            . "?grant_type=authorization_code"
            . "&code=$code"
            . "&redirect_uri=$redirectUri",
            'POST', 
            array(), 
            array(
                'Authorization' => 'Basic ' . base64_encode("$clientId:$clientSecret")
            )
        );
    }
    
    /**
     * Refreshes access token using given 
     * credentials
     * 
     * @param   string  $code        Code from allegro
     * @param   string  $clientId    Client ID
     * @param   string  $redirectUri Client secret
     * @return  object
     */
    public static function refreshToken($refreshToken, $clientId, $clientSecret, $redirectUri)
    {
        // Creating an instance of class
        $api = new AllegroRestApi(null, null);
        
        // Returning response
        return $api->sendRequest("https://allegro.pl/auth/oauth/token"
            . "?grant_type=refresh_token"
            . "&refresh_token=$refreshToken"
            . "&redirect_uri=$redirectUri",
            'POST', 
            array(), 
            array(
                'Authorization' => 'Basic ' . base64_encode("$clientId:$clientSecret")
            )
        );
    }

    /**
     * Stores token in class property to
     * use it in requests
     * 
     * @param   string  $value  Access token
     * @return  AllegroRestApi
     */
    public function setToken($value)
    {
        $this->token = $value;
        
        return $this;
    }

    /**
     * Returns api access token from property
     * 
     * @return  string  Access token
     */
    public function getToken()
    {
        return $this->token;
    }
    
    /**
     * Stores boolean in class property to
     * determine which environment should we use
     * 
     * @param   boolean  $value  True or false
     * @return  AllegroRestApi
     */
    public function setSandbox($value)
    {
        $this->sandbox = (boolean)$value;
        
        return $this;
    }

    /**
     * Returns boolean value which determines
     * which environment should we use
     * 
     * @return  boolean  True or false
     */
    public function getSandbox()
    {
        return $this->sandbox;
    }
    
    /**
     * Returns REST API basic URL depending
     * on current sandbox setting
     * 
     * @return  string  An URL address
     */
    public function getUrl()
    {
        // Returning correct URL depending on sandbox setting
        return $this->getSandbox() 
            ? AllegroRestApi::SANDBOX_URL 
            : AllegroRestApi::URL;
    }
    
    /**
     * Generates UUID which can be used in
     * some actions
     * 
     * @return  string  UUID
     */
    public function getUuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
    
    /**
     * Sends GET request to Allegro REST API
     * and returns response
     * 
     * @param   string  $resource   Resource path
     * @param   array   $headers    Request headers
     * @return  object
     */
    public function get($resource, array $headers = array())
    {
        return $this->sendRequest($resource, 'GET', array(), $headers);
    }

    /**
     * Sends POST request to Allegro REST API
     * and returns response
     * 
     * @param   string  $resource   Resource path
     * @param   array   $data       Request body
     * @param   array   $headers    Request headers
     * @return  object
     */
    public function post($resource, array $data, array $headers = array())
    {
        return $this->sendRequest($resource, 'POST', $data, $headers);
    }
    
    /**
     * Sends PUT request to Allegro REST API
     * and returns response
     * 
     * @param   string  $resource   Resource path
     * @param   array   $data       Request body
     * @param   array   $headers    Request headers
     * @return  object
     */
    public function put($resource, array $data, array $headers = array())
    {
        return $this->sendRequest($resource, 'PUT', $data, $headers);
    }
    
    /**
     * Sends DELETE request to Allegro REST API
     * and returns response
     * 
     * @param   string  $resource   Resource path
     * @param   array   $headers    Request headers
     * @return  object
     */
    public function delete($resource, array $headers = array())
    {
        return $this->sendRequest($resource, 'DELETE', array(), $headers);
    }
    
    /**
     * Sends request to Allegro REST API
     * using given arguments
     * 
     * Returns API response as JSON object
     * 
     * @param   string  $resource   Resource path
     * @param   string  $method     Request method
     * @param   array   $data       Request body
     * @param   array   $headers    Request headers
     * @return  object
     */
    public function sendRequest($resource, $method, array $data = array(), array $headers = array())
    {
        // Setting request options
        $options = array(
            'http' => array(
                'method'  => strtoupper($method),
                'header'  => $this->parseHeaders($requestHeaders = array_replace(array(
                    'User-Agent'      => 'Zoondo/AllegroApi/v2.0.0 (+https://www.zoondo.eu)',
                    'Authorization'   => 'Bearer ' . $this->getToken(),
                    'Content-Type'    => 'application/vnd.allegro.public.v1+json',
                    'Accept'          => 'application/vnd.allegro.public.v1+json',
                    'Accept-Language' => 'pl-PL'
                ), $headers)),
                'content' => json_encode($data),
                'ignore_errors' => true
            )
        );

        // Getting result from API
        $response = json_decode(file_get_contents(
            (stristr($resource, 'http') !== false 
                ? $resource 
                : $this->getUrl() . '/' . ltrim($resource, '/')
            ), 
            false, 
            stream_context_create($options)
        ));
        
        // We have found an error
        if (isset($response->errors)) {
            
            // Throwing an exception
            throw new Exception(
                'An error has occurred: ' . print_r($response->errors, true)
            );
        }
        
        // Checking if our response is a valid object
        if (!is_object($response)) {
            
            // Creating an instance of stdClass
            $response = new \stdClass();
        }
        
        // Saving response and request headers
        $response->request_headers  = $requestHeaders;
        $response->response_headers = $http_response_header;
        
        // Returning response
        return $response;
    }
        
    /**
     * Creates request headers as string
     * using given array
     * 
     * @return  string  Request headers
     */
    protected function parseHeaders(array $headers)
    {
        // Creating variable for headers
        $stringHeaders = '';
        
        // Loop over each of header
        foreach ($headers as $header => $value) {
            
            // Adding header line
            $stringHeaders .= "$header: $value\r\n";
        }
        
        // Returning headers
        return $stringHeaders;
    }
}
