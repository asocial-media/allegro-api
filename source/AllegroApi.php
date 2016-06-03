<?php
/**
 * Namespace declaration
 */
namespace Zoondo\AllegroApi;

/**
 * Used namespaces
 */
use SoapClient;
use InvalidArgumentException;

/**
 * Object PHP interface for Allegro WebAPI
 * 
 * This class allows you to call any WebAPI 
 * action in object way
 * 
 * It also implements some features which
 * will save you a lot of time
 * 
 * Just call correct WebAPI action with correct
 * paramters like you call class method
 * 
 * Example:
 * <pre>
 * // Trying to login into allegro account
 * $api->login(array(
 *     'userLogin'    => 'example',
 *     'userPassword' => 'examplepass',
 *     'countryCode'  => $api->getCountry(),
 *     'webapiKey'    => $api->getApiKey(),
 *     'localVersion' => $api->getVersionKey(),
 * ));
 * // For now, we have an access to $api->getSession()
 * 
 * // Getting our black list
 * $response = $api->doGetBlackListUsers(array(
 *     'sessionHandle' => $api->getSession(),
 * ));
 * 
 * // We can also omit "do" prefix
 * $response = $api->getBlackListUsers(array(
 *     'sessionHandle' => $api->getSession(),
 * ));
 * </pre>
 * 
 * @see     http://allegro.pl/webapi/documentation.php
 * @author  Maciej Strączkowski <m.straczkowski@gmail.com>
 * @version 1.0.0
 */
class AllegroApi
{
    /**
     * An URL for Allegro WSDL
     * 
     * You can change this value by using setWsdlUrl method
     * It was developed in case of Allegro WebAPI changes
     */
    const WSDL = 'https://webapi.allegro.pl/service.php?wsdl';
    
    /**
     * An URL for Allegro Sandbox WSDL
     * 
     * You can change this value by using setWsdlUrl method
     * It was developed in case of Allegro WebAPI changes
     */
    const WSDL_SANDBOX = 'https://webapi.allegro.pl.webapisandbox.pl/service.php?wsdl';
    
    /**
     * Country constant
     */
    const ALLEGRO_POLAND = 1;
    
    /**
     * Allegro WebAPI key
     * @var string
     */
    protected $apiKey = null;
    
    /**
     * Allegro country id number (1 - Poland)
     * @var integer
     */
    protected $country = 1;
    
    /**
     * Allegro session identifier
     * @var string
     */
    protected $session = null;
    
    /**
     * Current WSDL Url
     * @var string
     */
    protected $wsdlUrl = null;
    
    /**
     * An instance of SoapClient
     * 
     * @var SoapClient
     */
    protected $soap = null;
    
    /**
     * Method requires an api key as first
     * argument
     * 
     * This method saves given value into 
     * class property
     * 
     * So you need to provide an api key
     * to create an instance of class
     * 
     * @param   string  $apiKey   Api key
     * @param   integer $country  Country
     * @param   boolean $sandbox  Sandbox
     */
    public function __construct($apiKey, $country = self::ALLEGRO_POLAND, $sandbox = false)
    {
        // Setting an api key
        $this->setApiKey($apiKey);
        
        // Setting country
        $this->setCountry($country);
        
        // Setting correct WSDL URL
        $this->setWsdlUrl(($sandbox 
            ? self::WSDL_SANDBOX 
            : self::WSDL
        ));
        
        // Creating an instance of SoapClient
        $this->setSoapClient(new SoapClient($this->getWsdlUrl(), array(
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS
        )));
        
        // Setting version key
        $this->setVersionKey(
            $this->fetchVersionKey()
        );
    }

    /**
     * Method saves given api key into
     * class property
     * 
     * It returns an instance of class
     * so you can use method chaining
     * 
     * It also allows you to change an
     * api key whenever you want to
     * 
     * @param   string  $apiKey  Api key
     * @return  AlegroApi
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        
        return $this;
    }
    
    /**
     * Method returns an api key which
     * is stored in property
     * 
     * This value always exists because
     * constructor requires it
     * 
     * You can use it to access api key
     * whenever you want to
     * 
     * @return  string  Allegro api key
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }
    
    /**
     * Method saves given version key 
     * into class property
     * 
     * It returns an instance of class
     * so you can use method chaining
     * 
     * It also allows you to change an
     * version key whenever you want to
     * 
     * @param   string  $versionKey  Version key
     * @return  AlegroApi
     */
    public function setVersionKey($versionKey)
    {
        $this->versionKey = $versionKey;
        
        return $this;
    }
    
    /**
     * Method returns version key which
     * is stored in property
     * 
     * This value always exists because
     * constructor creates it
     * 
     * You can use it to access version
     * key whenever you want to
     * 
     * @return  string  Version key
     */
    public function getVersionKey()
    {
        return $this->versionKey;
    }
    
    /**
     * Method saves given country code
     * into class property
     * 
     * It returns an instance of class
     * so you can use method chaining
     * 
     * It also allows you to change an
     * country whenever you want to
     * 
     * @param   integer  $country  Country
     * @return  AlegroApi
     */
    public function setCountry($country)
    {
        $this->country = (int)$country;
        
        return $this;
    }
    
    /**
     * Method returns country code which
     * is stored in property
     * 
     * This value always exists because
     * constructor requires it
     * 
     * You can use it to access country
     * code whenever you want to
     * 
     * @return  integer  Country
     */
    public function getCountry()
    {
        return $this->country;
    }
    
    /**
     * Method saves given WSDL into
     * class property
     * 
     * It returns an instance of class
     * so you can use method chaining
     * 
     * It shoudn't be used till default
     * WSDL URL works properly
     * 
     * @param   string  $wsdl  WSDL URL
     * @return  AlegroApi
     */
    public function setWsdlUrl($wsdl)
    {
        $this->wsdlUrl = $wsdl;
        
        return $this;
    }
    
    /**
     * Method returns WSDL URL which
     * is stored in property
     * 
     * It shoudn't be used till default
     * WSDL URL works properly
     * 
     * @return  string  WSDL URL
     */
    public function getWsdlUrl()
    {
        return $this->wsdlUrl;
    }
    
    /**
     * Method saves given session id into
     * class property
     * 
     * It returns an instance of class
     * so you can use method chaining
     * 
     * It also allows you to change session
     * identifier whenever you want to
     * 
     * @param   string  $session  Session
     * @return  AlegroApi
     */
    public function setSession($session)
    {
        $this->session = $session;
        
        return $this;
    }
    
    /**
     * Method returns a session identifier
     * which is stored in property
     * 
     * This value may not exist if user is
     * not currently logged in
     * 
     * This value is active for 60 minutes
     * 
     * You can use it to access session id
     * whenever you want to
     * 
     * @return  string  Session identifier
     */
    public function getSession()
    {
        return $this->session;
    }
    
    /**
     * Method saves given instance of 
     * SoapClient into class property
     * 
     * It returns an instance of class
     * so you can use method chaining
     * 
     * This method shoudn't be used if
     * everything works by default
     * 
     * @param   SoapClient  $soap
     * @return  AlegroApi
     */
    public function setSoapClient(SoapClient $soap)
    {
        $this->soap = $soap;
        
        return $this;
    }
    
    /**
     * Method returns SoapClient which
     * is stored in property
     * 
     * This method shoudn't be used if
     * everything works by default
     * 
     * @return  SoapClient
     */
    public function getSoapClient()
    {
        return $this->soap;
    }
    
    /**
     * Method calls doLogin action on api
     * 
     * It saves session identifier from api
     * response into class property
     * 
     * It works almost the same as standard
     * WebAPI login action
     * 
     * @see     http://allegro.pl/webapi/documentation.php/show/id,82
     * @param   array  $parameters  Parameters
     * @return  WebAPI Response
     */
    public function login(array $parameters)
    {
        // Executing request to WebAPI
        $session = $this->getSoapClient()->doLogin($parameters);
        
        // Setting session id into class property
        $this->setSession($session->sessionHandlePart);
        
        // Returning session
        return $session;
    }
    
    /**
     * Method calls doLoginEnc action on api
     * 
     * It saves session identifier from api
     * response into class property
     * 
     * It works almost the same as standard
     * WebAPI doLoginEnc action
     * 
     * @see     http://allegro.pl/webapi/documentation.php/show/id,83
     * @param   array  $parameters  Parameters
     * @return  WebAPI Response
     */
    public function loginEnc(array $parameters)
    {
        // Executing request to WebAPI
        $session = $this->getSoapClient()->doLoginEnc($parameters);
        
        // Setting session id into class property
        $this->setSession($session->sessionHandlePart);
        
        // Returning session
        return $session;
    }
    
    /**
     * This method is an alias for login
     * 
     * It was developed to overwrite api
     * action call via this class
     * 
     * @see     http://allegro.pl/webapi/documentation.php/show/id,82
     * @return  array   WebAPI response
     */
    public function doLogin(array $parameters)
    {
        return $this->login($parameters);
    }
    
    /**
     * This method is an alias for loginEnc
     * 
     * It was developed to overwrite api
     * action call via this class
     * 
     * @see     http://allegro.pl/webapi/documentation.php/show/id,83
     * @return  array   WebAPI response
     */
    public function doLoginEnc(array $parameters)
    {
        return $this->loginEnc($parameters);
    }
    
    /**
     * Method allows you to execute any
     * Allegro WebAPI action
     * 
     * Just execute method which name
     * is the same as WebAPI action
     * 
     * You can provide action name with
     * prefix "do" or without prefix
     * 
     * @param   string   WebAPI action
     * @param   array    An arguments
     */
    public function __call($action, array $arguments)
    {
        // Creating parameters
        $parameters = (isset($arguments[0])
            ? $arguments[0] 
            : array()
        );

        // Throwing an exception if not array
        if (!is_array($parameters)) {
            
            // Throwing an exception
            throw new InvalidArgumentException(
                'Specified parameters must be an array, '
                    .gettype($parameters)
                .' given'
            );
        }
        
        // Getting correct action name
        $function = $this->resolveActionName($action);
        
        // Returning WebAPI response
        return $this->getSoapClient()->$function($parameters);
    }

    /**
     * Method checks if given action name
     * has defined prefix
     * 
     * It adds prefix if it doesn't exist
     * or returns the same string otherwise
     * 
     * This resolver allows you to pass
     * action names with or without prefix 
     * 
     * @param   string   $action  WebApi action
     * @return  string   WebApi action
     */
    protected function resolveActionName($action)
    {
        // Returning action name
        if (substr($action, 0, 2) === 'do') {
            return $action;
        }
        
        // Returning action name with prefix
        return 'do'.ucfirst($action);
    }
    
    /**
     * Method requests Allegro WebAPI to get
     * correct version key
     * 
     * It sends current API key and current
     * country code in request
     * 
     * It returns correct version key or throws
     * if specified country code is incorrect
     * 
     * @return  string  Version key
     * @throws  InvalidArgumentException
     */
    protected function fetchVersionKey()
    {
        // Executing request to WebAPI
        $response = $this->getSoapClient()->doQueryAllSysStatus(array(
            'countryId' => $this->getCountry(),
            'webapiKey' => $this->getApiKey()
        ));
        
        // Loop over records from response
        foreach ($response->sysCountryStatus->item as $row) {
         
            // Returning correct version key
            if ($row->countryId == $this->getCountry()) {
                return $row->verKey;
            }
        }
        
        // Throwing an exception if country code is incorrect
        throw new InvalidArgumentException(
            'Specified country code is incorrect'
        );
    }
}
