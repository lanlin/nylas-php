# [![Nylas-PHP](media/nylas-php.png)](https://github.com/lanlin/nylas-php)

# Nylas PHP SDK

PHP bindings for the Nylas REST API (V2.0). https://docs.nylas.com/reference

Here's why not use the official version
# [![Nylas-Official](media/not-use.png)](https://github.com/nylas/nylas-php)

**1. All Nylas APIs have been implemented within this SDK.**
**2. Chained calls and good code hints**

Automatic incomplete effect under phpstorm:
`$nylas->Accounts()->Account()->`
![Nylas-PHP](media/nylas-php.png)

`$nylas->Accounts()->Manage()->`
![Nylas-PHP](media/nylas-php.png)


## Installation

This library is available on http://packagist.org. You can install it by running

```shell
composer require lanlin/nylas-php
```


## Usage

### App ID and Secret

Before you can interact with the Nylas REST API,
you need to create a Nylas developer account at [https://www.nylas.com/](https://www.nylas.com/).
After you've created a developer account, you can create a new application to generate an App ID / Secret pair.

Generally, you should store your App ID and Secret into environment variables to avoid adding them to source control.
The test projects use configuration files instead, to make it easier to get started.

### Init Nylas-PHP

```php
use Nylas/Client;

$options =
[
    'debug'         => true,
    'log_file'      => dirname(__FILE__) . '/test.log',
    'account_id'    => 'your account id',
    'access_token'  => 'your access token',
    'client_id'     => 'your client id',        // required
    'client_secret' => 'your client secret'     // required
];

$nylas = new Client($options);
```


### Authentication

There are two ways you can authenticate users to your application.
Hosted & Native are both supported.

Here's the server-side(three-legged) OAuth example:

1. You redirect the user to nylas login page, along with your App Id and Secret
2. Your user logs in
3. She is redirected to a callback URL of your own, along with an access code
4. You use this access code to get an authorization token to the API

For more information about authenticating with Nylas,
visit the [Developer Documentation](https://docs.nylas.com/reference#authentication).

In practice, the Nylas REST API client simplifies this down to two steps.

**Step 1: Redirect the user to Nylas:**

```php
$params =
[
    'state'        => 'testing',
    'login_hint'   => 'test@gmail.com',
    'redirect_uri' => 'https://www.test.com/redirect_callback',
];

// generate the url that your user need be redirect to.
$url = $nylas->Authentication()->Hosted()->getOAuthAuthorizeUrl($params);
```

**Step 2: your user logs in:**
**Step 3: you got the access code from the nylas callback:**
Please implement the above 2 & 3 steps yourself.

**Step 4: Get authorization token with access code:**

```php
$data = $nylas->Authentication()->Hosted()->postOAuthToken($params);

// save your token some where
// or update the client option
$nylas->Options()->setAccessToken('the token you'v got');
```


### Accounts

Accounts Methods:
`$nylas->Accounts()->Account()->`
![Nylas-PHP](media/nylas-php.png)

Manage Methods:
`$nylas->Accounts()->Manage()->`
![Nylas-PHP](media/nylas-php.png)


### Calendars
**... @todo ...**


## Contributing

For more usage demos, please view the tests.
Please feel free to use it and send me a pull request if you fix anything or add a feature, though. :)


## License

This project is licensed under the MIT license.
