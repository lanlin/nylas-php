<div align="center">
  <img width="635" height="217" src="media/nylas-php.png" />
</div>

# Nylas PHP SDK (latest 5.3.0, see [change-log](https://github.com/lanlin/nylas-php/releases))

[![Build](https://github.com/lanlin/nylas-php/workflows/Testing/badge.svg?branch=master)](https://github.com/lanlin/nylas-php/actions)
[![Code Quality](https://github.com/lanlin/nylas-php/workflows/CodeQuality/badge.svg?branch=master)](https://github.com/lanlin/nylas-php/actions)
[![Packagist Version (including pre-releases)](https://img.shields.io/packagist/v/lanlin/nylas-php?include_prereleases)](https://packagist.org/packages/lanlin/nylas-php)
[![Packagist Stars](https://img.shields.io/packagist/stars/lanlin/nylas-php)](https://packagist.org/packages/lanlin/nylas-php)
[![Total Downloads](https://img.shields.io/packagist/dt/lanlin/nylas-php)](https://packagist.org/packages/lanlin/nylas-php)
[![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/lanlin/nylas-php)](https://packagist.org/packages/lanlin/nylas-php)
[![License](https://img.shields.io/github/license/lanlin/nylas-php)](https://github.com/lanlin/nylas-php#license)

PHP bindings for the Nylas REST API (V2.7). https://developer.nylas.com/docs/api/v2/</br>
I'll try to keep up with [NyLas API Changelog](https://developer.nylas.com/docs/new/release-notes/) in future updates.

Last check at the point: [[2023-05-16] Improvements to Scheduler and changes to message link tracking](https://developer.nylas.com/docs/new/release-notes/2023-05-16-link-tracking-changes/)

**What's new?**</br>

1. API 2.7 support</br>
2. All Nylas APIs have been implemented within this SDK.</br>
3. Support send & get message in raw type</br>
4. Support async batch upload & download</br>
   -- Contact picture download</br>
   -- File upload & download<br>
5. The parameters that required by methods almost the same as nylas official api required.</br>
6. Support async batch get & delete & send
7. Chained calls and good code hints, easy to use</br>

## Installation (PHP 8.0 required since version 5.0)

**version 3.x for php >= 7.3 (branch 3.0)**

**version 4.x for php >= 7.4 (branch 4.0)**

**version 5.x for php >= 8.0 (branch master)**

Tips: There are many breaking changes since version 5.0

1. all methods name changed (named same as the nylas API doc describe title)
2. the smart methods removed
3. many other changes

This library is available on https://packagist.org/packages/lanlin/nylas-php</br>
You can install it by running

```shell
composer require lanlin/nylas-php
```


## Usage

### App ID and Secret

Before you can interact with the Nylas REST API,</br>
you need to create a Nylas developer account at [https://www.nylas.com/](https://www.nylas.com/).</br>
After you've created a developer account, you can create a new application to generate an App ID / Secret pair.</br>

Generally, you should store your App ID and Secret into environment variables to avoid adding them to source control.</br>
The test projects use configuration files instead, to make it easier to get started.</br>

### Init Nylas-PHP

```php
use Nylas\Client;

$options =
[
    'client_id'        => 'your client id',    // required
    'client_secret'    => 'your client secret' // required
    
    'debug'            => true,
    'region'           => 'oregon', // server region, can be oregon of United States, ireland of Europe, default is oregon
    'log_file'         => dirname(__FILE__) . '/test.log',  // a file path or a resource handler
    'access_token'     => 'your access token',
];

$nylas = new Client($options);
```

### Options Setting

You can modify options with methods of `\Nylas\Utilities\Options`

```php
$nylas->Options->setXxx();
```

### Batch Request

Most of the methods that have the get & delete prefix support batch request.

```php
$id  = 'id_xxx';
$ids = ['id_xxx', 'id_yyy', ...];

// one per time
$dataA = $nylas->Contacts->Contact->returnAContact($id);
$dataB = $nylas->Contacts->Contact->deleteAContact($id);

// batch request
$dataC = $nylas->Contacts->Contact->returnAContact($ids);
$dataD = $nylas->Contacts->Contact->deleteAContact($ids);
```

For more detail about the batch request, you should have to read the source code.</br>
Sorry, I have no time to write documents.


### Authentication

There are two ways you can authenticate users to your application.</br>
Hosted & Native are both supported.</br>

For Native Authentication example, please visit [Native Auth Document](https://developer.nylas.com/docs/the-basics/authentication/native-authentication/) </br>
For Hosted OAuth(server-side three-legged) example:</br>

1. You redirect the user to nylas login page, along with your App Id and Secret</br>
2. Your user logs in</br>
3. She is redirected to a callback URL of your own, along with an access code</br>
4. You use this access code to get an authorization token to the API</br>

For more information about authenticating with Nylas,</br>
visit the [Developer Documentation](https://docs.nylas.com/reference#authentication).</br>

In practice, the Nylas REST API client simplifies this down to two steps.</br>

**Step 1: Redirect the user to Nylas:**

```php
$params =
[
    'state'        => 'testing',
    'login_hint'   => 'test@gmail.com',
    'redirect_uri' => 'https://www.test.com/redirect_callback',
];

// generate the url that your user need be redirect to.
$url = $nylas->Authentication->Hosted->authenticateUser($params);
```

**Step 2: your user logs in:**</br>
**Step 3: you got the access code from the nylas callback:**</br>
Please implement the above 2 & 3 steps yourself.</br>

**Step 4: Get authorization token with access code:**

```php
$data = $nylas->Authentication->Hosted->sendAuthorizationCode($params);

// save your token some where
// or update the client option
$nylas->Options->setAccessToken("pass the token you got");
```


## Error & Exceptions

1. common error codes that response from nylas are wrapped as exceptions, (see `src/Exceptions`)
   and the exception code is the same as [nylas api error list](https://docs.nylas.com/reference#errors)

2. you will get an array like below, when response data was not a valid json string or even not json content type:

   ```php
   [
       'httpStatus'  => 'http status code',
       'invalidJson' => true,
       'contentType' => 'response header content type',
       'contentBody' => 'response body content',
   ]
   ```

3. for all methods that execute as the async mode will not throw an exception when an error occurs,
   instead, it will return an array which contains all data and exceptions inside like below:

   ```php
   [
       // ...
       [
           'error'     => true,
           'code'      => 'exception code',
           'message'   => 'exception message',
           'exception' => 'exception instance',
       ],
       // ...
   ]
   ```

4. some email provider may not support all features, exp: calendar, event.
   for that reason you may get an exception named `BadRequestException` with 400 code and the msg:

   ```shell
   Malformed or missing a required parameter, or your email provider not support this.
   ```

5. the `log_file` parameter only works when `debug` set to `true`,
   then the detailed info of the http request will be logged.

   Tips:
   nylas-php use the guzzlehttp for http request.
   but guzzlehttp only support a resource type as the debug handler (cURL CURLOPT_STDERR required that).

   for anyone who wants to use psr/log interface to debug,
   you can init a temp resource, and pass the handler to nylas-php,
   then get log content from temp resource after calling some methods.

   ```php
   $handler = fopen('php://temp', 'w+');

   $options =
   [
       'log_file' => $handler,
       ...
   ];

   $nylas = new Client($options);
   $nylas->doSomething();
   ....

   rewind($handler);
   $logContent = stream_get_contents($handler);
   fclose($handler);

   $yourPsrLogger->debug($logContent);
   ```


## Launching the tests

1. Initialise composer dependency `composer install`
2. Add your info in `tests/AbsCase.php`
3. Launch the test with `composer run-script test`
4. Another way to run tests: `./tests/do.sh foo.php --filter fooMethod`, see `tests/do.sh`


## Methods & Parameters

Each method's comment has a link to their specific API document, see [here](https://github.com/lanlin/nylas-php/issues/44)

The parameters that required by methods almost the same as nylas official api required.

For more detail, you can view the tests or the source code of validation rules for that method.

### Apis Of Accounts & Account Management & Application Management

```php
$nylas->Management->Account->xxx();
$nylas->Management->Application->xxx();
```

## Contributing

For more usage demos, please view the tests.</br>
Please feel free to use it and send me a pull request if you fix anything or add a feature, though.</br>

## License

This project is licensed under the MIT license.
