<?php

namespace PHPCraftdream\FigCookies;

use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ResponseInterface;

interface CookiesInterface {
	public function __construct();

	public function has($name): bool;

	public function get($name): CookieInterface;

	public function getAll(): array;

	public function add(CookieInterface $cookie): CookiesInterface;

	public function delete(string $name): CookiesInterface;

	public function toResponse(ResponseInterface $response): ResponseInterface;

	public function fromResponse(ResponseInterface $response): CookiesInterface;

	public function toRequest(RequestInterface $request): RequestInterface;

	public function fromRequest(RequestInterface $request): CookiesInterface;

	public function fromCookieStrings(array $cookieStrings): CookiesInterface;
}
