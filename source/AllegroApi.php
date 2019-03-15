<?php
/**
 * Namespace declaration
 */
namespace Zoondo\AllegroApi;

/**
 * Class created for back compatibility
 * in order to organize class names
 * 
 * @see        http://allegro.pl/webapi/documentation.php
 * @author     Maciej Strączkowski <m.straczkowski@gmail.com>
 * @copyright  ZOONDO.EU Maciej Strączkowski
 * @version    2.0.0
 */
class AllegroApi extends AllegroWebApi
{
    /**
     * Returns an instance of AllegroWebApi
     * 
     * @return  AllegroWebApi
     */
    public function webApi()
    {
        return $this;
    }
    
    /**
     * Returns an instance of AllegroRestApi
     * 
     * @return  AllegroRestApi
     */
    public function restApi($token, $sandbox = false)
    {
        return new AllegroRestApi($token, $sandbox);
    }
}
