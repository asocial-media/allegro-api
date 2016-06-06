# README

The PHP interface which allows you to communicate with **Allegro WebAPI**

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

## Basic usage

```php
// Used namespace
use Zoondo\AllegroApi\AllegroApi;

// Creating new instance of AllegroApi
$api = new AllegroApi('your_api_key');

// We are going to login
$api->login(array(
    'userLogin'    => 'example',
    'userPassword' => 'examplepass',
    'countryCode'  => $api->getCountry(),
    'webapiKey'    => $api->getApiKey(),
    'localVersion' => $api->getVersionKey(),
));
// For now, we have an access to $api->getSession()

// Getting our current black list
$response = $api->doGetBlackListUsers(array(
    'sessionHandle' => $api->getSession(),
));

// We canm also omit "do" prefix in method names
$response = $api->getBlackListUsers(array(
    'sessionHandle' => $api->getSession(),
));

```

## Unit testing

To run unit tests just execute the following command

```bash
$ php phpunit.phar ./tests
```

## Authors

- Maciej Strączkowski - <m.straczkowski@gmail.com>

## License

The files in this archive are released under the [MIT LICENSE](LICENSE).
