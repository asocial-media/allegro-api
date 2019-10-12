# CHANGELOG

## 3.0.0 (2019-10-12)

- Re-branded repository / organisation from "zoondo" to "asocial-media"
- Added possibility to send raw data (text, binary etc) instead of JSON only via put(), post(), sendRequest() methods ($data parameter is no longer required as array and there is new parameter $json which tells that we should send request as json (default: true)
- Fixed some typos

# CHANGELOG

## 2.0.5 (2019-03-26)

- Added rest api error handling with exceptions

## 2.0.0 (2019-03-15)

- Added interface for REST API
- Added examples for REST API usage in README
- Reorganized class names with back compatibility
- Fixed some typos in comments
- Fixed SOAP "broken pipe error" which sometimes occurres

## 1.0.3 (2018-06-19)

- Implemented new login method using access token

## 1.0.2 (2018-01-31)

- Changed licensor in LICENSE file
- Changed version number in *.php file
- Added @copyright into *.php file
- Removed unit testing paragraph from README

## 1.0.1 (2016-06-18)

- Fixed .gitignore and .editorconfig
- Added CHANGELOG

## 1.0.0 (2016-06-06)

- First stable release
