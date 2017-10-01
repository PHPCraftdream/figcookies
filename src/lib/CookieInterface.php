<?php

namespace PHPCraftdream\FigCookies;

use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ResponseInterface;

interface CookieInterface
{
	public function __construct($name = NULL, $value = NULL);
	public function setItNew(): CookieInterface;
	public function isNew(): bool;
	public function isChanged(): bool;
	public function resetChanged();
	public function getName();
	public function getValue();
	public function getExpires();
	public function getMaxAge();
	public function getPath();
	public function getDomain();
	public function getSecure();
	public function getHttpOnly();
	public function setName(string $name = NULL): CookieInterface;
	public function setValue(string $value = NULL): CookieInterface;
	public function setExpires($expires = NULL): CookieInterface;
	public function rememberForever(): CookieInterface;
	public function expire(): CookieInterface;
	public function setMaxAge($maxAge = NULL): CookieInterface;
	public function setPath($path = NULL): CookieInterface;
	public function setDomain($domain = NULL): CookieInterface;
	public function setSecure($secure = NULL): CookieInterface;
	public function setHttpOnly($httpOnly = NULL): CookieInterface;
	public function __toString();
	public function parse(string $string): CookieInterface;
}
