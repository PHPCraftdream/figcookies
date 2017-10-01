FIG Cookies
===========

Managing Cookies for PSR-7 Requests and Responses. It is fork from [dflydev/dflydev-fig-cookies](https://github.com/dflydev/dflydev-fig-cookies)

[![Build Status](https://api.travis-ci.org/PHPCraftdream/figcookies.svg?branch=master)](https://travis-ci.org/PHPCraftdream/figcookies)

Requirements
------------

 * PHP 7.1+
 * [psr/http-message](https://packagist.org/packages/psr/http-message)


Installation
------------

```bash
$> composer require phpcraftdream/figcookies
```

Concepts
--------
Instantiating these collections looks like this:

```php
use PHPCraftdream\FigCookies\Cookies;

// Get cookies from request
$cookies = (new Cookies)->fromRequest($request);

// Get cookies from response
$cookies = (new Cookies)->fromResponse($response);
```

After modifying these collections in some way, they are rendered into a
PSR-7 Request or PSR-7 Response like this:

```php
// Render the Cookie headers and add them to the headers of a
// PSR-7 Request.
$request = $cookies->toRequest($request);

// Render the Set-Cookie headers and add them to the headers of a
// PSR-7 Response.
$response = $cookies->toResponse($response);
```

Basic Usage
-----------
#### Get a Request Cookie

The `get` method will return a `Cookie` instance. If no cookie by the specified
name exists, the returned `Cookie` instance will have new empty `Cookie` instance.

```php
use PHPCraftdream\FigCookies\Cookies;

$cookie = (new Cookies)->fromRequest($request)->get('theme');
$cookie->isNew(); //true if no cookie by the specified name exists
//...
$cookie->getValue();
```

#### Set a Request Cookie

The `set` method will either add a cookie or replace an existing cookie.

The `Cookie` primitive is used as the second argument.

```php
use PHPCraftdream\FigCookies\Cookies;

$cookies = (new Cookies)->fromRequest($request);
$cookies->get('theme')->setValue('acme');

$request = $cookies->toRequest($request);
```

#### Remove a Request Cookie

The `remove` method removes a cookie if it exists.

```php
use PHPCraftdream\FigCookies\Cookies;

$cookies = (new Cookies)->fromRequest($request);
$cookies->delete('theme');
$request = $cookies->toRequest($request);
```

### Create cookie

```php
use PHPCraftdream\FigCookies\Cookie;

$cookie = (new Cookie('lu'))
	->setValue('Rg3vHJZnehYLjVg7qi3bZjzg')
	->setExpires('Tue, 15-Jan-2013 21:47:38 GMT')
	->setMaxAge(500)
	->rememberForever()
	->setPath('/')
	->setDomain('.example.com')
	->setSecure(true)
	->setHttpOnly(true)
;
```

#### Get a Response Cookie


```php
use PHPCraftdream\FigCookies\Cookies;

$cookies = (new Cookies)->fromResponse($response);
$cookie = $cookies->get('theme');
```

#### Set a Response Cookie

The `add` method will either add a cookie or replace an existing cookie.

```php
use PHPCraftdream\FigCookies\Cookie;
use PHPCraftdream\FigCookies\Cookies;

$cookies = (new Cookies)->fromResponse($response);

$response = $cookies
	->add(
		(new Cookie('token'))
		->setValue('a9s87dfz978a9')
		->setDomain('example.com')
		->setPath('/firewall')
	)
	->toResponse($response);
```

#### Modify a Response Cookie

```php
use PHPCraftdream\FigCookies\Cookies;

$cookies = (new Cookies)->fromResponse($response);

$cookie = $cookies->get('visitCount');
$cookie->setValue($cookie->getValue() + 1);

$response = $cookies->toResponse($response);
```

#### Remove a Response Cookie

The `delete` method removes a cookie from the response if it exists.

```php
use PHPCraftdream\FigCookies\Cookies;

$response = (new Cookies)
				->fromResponse($response)
				->delete('theme')
				->toResponse($response);
```

#### Expire a Response Cookie

The `expire` method sets a cookie with an expiry date in the far past. This
causes the client to remove the cookie.

```php
use PHPCraftdream\FigCookies\Cookies;

$cookies = (new Cookies)->fromResponse($response);
$cookies->get('session_cookie')->expire();
$response = $cookies->toResponse($response);
```

FAQ
---

### Do you call `setcookies`?

No.

Delivery of the rendered `Cookies` instances is the responsibility of the
PSR-7 client implementation.


### Do you do anything with sessions?

No.

It would be possible to build session handling using cookies on top of FIG
Cookies but it is out of scope for this package.


### Do you read from `$_COOKIES`?

No.

FIG Cookies only pays attention to the `Cookie` headers on PSR-7 Request
instances.


License
-------

MIT, see LICENSE.