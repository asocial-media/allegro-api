# README

The PHP interface which allows you to communicate with **Allegro WebAPI and RestApi**

## Requirements

- PHP >= 5.2.4

## Installation

### Composer (recommended)

```bash
$ composer require zoondo/allegro-api
```

### Manually

```bash
$ git clone https://github.com/zoondo/allegro-api.git
```

or just [download zip archive](https://github.com/zoondo/allegro-api/archive/master.zip)

## Allegro WebApi basic usage

```php
// Used namespace
use Zoondo\AllegroApi\AllegroApi;

// Creating new instance of AllegroApi
$api = new AllegroApi('your_api_key');

// We are going to login using Allegro WebApi
$api->login(array(
    'userLogin'    => 'example',
    'userPassword' => 'examplepass',
    'countryCode'  => $api->getCountry(),
    'webapiKey'    => $api->getApiKey(),
    'localVersion' => $api->getVersionKey(),
));
// For now, we have an access to $api->getSession()

// Getting our current black list using Allegro WebApi
$response = $api->doGetBlackListUsers(array(
    'sessionHandle' => $api->getSession(),
));

// We can also omit "do" prefix in method names
$response = $api->getBlackListUsers(array(
    'sessionHandle' => $api->getSession(),
));
```

## Allegro RestApi basic usage

```php
// Used namespace
use Zoondo\AllegroApi\AllegroRestApi;

// Register your application here:
// https://apps.developer.allegro.pl

// Creating auth URL using client id and redirect_uri
var_dump(
    AllegroRestApi::getAuthLink($clientId, $redirectUri)
);

// After clicking above link you need to grant permission and then
// you will be redirected to:
//     $redirectUri with special code in URL (GET)
//
// Use above code to finally generate your access token
$tokens = AllegroRestApi::generateToken(
    (isset($_GET['code']) ? $_GET['code'] : null), 
    $clientId, 
    $clientSecret, 
    $redirectUri
);

// Token will be active for 12 hours and you can refresh 
// it indefinitely (via cron job)
AllegroRestApi::refreshToken(
    $tokens->refresh_token, 
    $clientId, 
    $clientSecret, 
    $redirectUri
);

// If we have our access token we can finally request REST API

// Creating an instance of RestApi
$restApi = new AllegroRestApi($tokens->access_token);

// Getting our comments
$response = $restApi->get('/sale/user-ratings?user.id=' . $yourUserId);

// Creating 2 variants using color/pattern
$response = $restApi->put('/sale/offer-variants/' . $restApi->getUuid(), array(
    'name'       => 'Wariant 1',
    'offers'     => array(
        array(
            'id'           => '6846772956',
            'colorPattern' => 'yellow_gold'
        ),
        array(
            'id'           => '6846772915',
            'colorPattern' => 'white_rhodium'
        )
    ),
    'parameters' => array(
        array(
            'id' => 'color/pattern'
        ),
    )
), array(
    'Content-Type' => 'application/vnd.allegro.beta.v1+json', // Some actions needs version change from "public" to "beta"
    'Accept'       => 'application/vnd.allegro.beta.v1+json', // Some actions needs version change from "public" to "beta"
));
```

## Authors

- Maciej Strączkowski - <m.straczkowski@gmail.com>

## License

The files in this archive are released under the [MIT LICENSE](LICENSE).
