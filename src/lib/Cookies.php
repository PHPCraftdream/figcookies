<?php

namespace PHPCraftdream\FigCookies;

use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ResponseInterface;

class Cookies implements CookiesInterface
{
	use StringUtilTrait;

	const SET_COOKIE_HEADER = 'Set-Cookie';
	const COOKIE_HEADER = 'Cookie';

	protected $cookies = [];

	public function __construct(array $cookies = [])
	{
		foreach ($cookies as $cookie)
			$this->cookies[$cookie->getName()] = $cookie;
	}

	public function has($name): bool
	{
		return isset($this->cookies[$name]);
	}

	public function get($name): CookieInterface
	{
		if (!$this->has($name))
			$this->cookies[$name] = $this->newCookie($name)->setItNew();

		return $this->cookies[$name];
	}

	public function getAll(): array
	{
		return array_values($this->cookies);
	}

	public function add(CookieInterface $cookie): CookiesInterface
	{
		$this->cookies[$cookie->getName()] = $cookie;

		return $this;
	}

	public function delete(string $name): CookiesInterface
	{
		if (!$this->has($name))
			return $this;

		unset($this->cookies[$name]);
		return $this;
	}

	public function toResponse(ResponseInterface $response): ResponseInterface
	{
		$response = $response->withoutHeader(self::SET_COOKIE_HEADER);

		foreach ($this->cookies as $cookie)
			$response = $response->withAddedHeader(self::SET_COOKIE_HEADER, (string) $cookie);

		return $response;
	}

	public function fromResponse(ResponseInterface $response): CookiesInterface
	{
		$cookieStrings = $response->getHeader(self::SET_COOKIE_HEADER);
		$this->fromCookieStrings($cookieStrings);

		return $this;
	}

	public function toRequest(RequestInterface $request): RequestInterface
	{
		$cookieString = implode('; ', $this->cookies);

		$request = $request->withHeader(self::COOKIE_HEADER, $cookieString);

		return $request;
	}

	public function fromRequest(RequestInterface $request): CookiesInterface
	{
		$cookieString = $request->getHeaderLine(self::COOKIE_HEADER);
		$this->parseCookieString($cookieString);

		return $this;
	}

	protected function newCookie($name = NULL): CookieInterface
	{
		return new Cookie($name);
	}

	protected function parseCookieString(string $string): CookiesInterface
	{
		$cookiesStrArr = $this->splitOnAttributeDelimiter($string);

		foreach ($cookiesStrArr as $cookieStr)
		{
			$cookie = $this->newCookie();
			$cookie->parse($cookieStr);
			$this->add($cookie);
		}

		return $this;
	}

	public function fromCookieStrings(array $cookieStrings): CookiesInterface
	{
		$this->cookies = [];

		foreach ($cookieStrings as $cookieStr)
		{
			$cookie = $this->newCookie();
			$cookie->parse($cookieStr);

			$this->cookies[$cookie->getName()] = $cookie;
		}

		return $this;
	}
}
