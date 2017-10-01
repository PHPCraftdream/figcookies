<?php

namespace PHPCraftdream\FigCookies;

class CookiesTest extends \PHPUnit_Framework_TestCase {
	const INTERFACE_PSR_HTTP_MESSAGE_RESPONSE = 'Psr\Http\Message\ResponseInterface';

	/**
	 * @param string[] $cookieStrings
	 * @param Cookie[] $expectedCookies
	 *
	 * @test
	 * @dataProvider provideCookieStringsAndExpectedCookiesData
	 */
	public function it_creates_from_response($cookieStrings, array $expectedCookies) {
		$response = $this->prophesize(static::INTERFACE_PSR_HTTP_MESSAGE_RESPONSE);
		$response->getHeader(Cookies::SET_COOKIE_HEADER)->willReturn($cookieStrings);

		$cookies = (new Cookies())->fromResponse($response->reveal());

		$this->assertEquals($expectedCookies, $cookies->getAll());
	}

	/**
	 * @param string[] $cookieStrings
	 * @param Cookie[] $expectedCookies
	 *
	 * @test
	 * @dataProvider provideCookieStringsAndExpectedCookiesData
	 */
	public function it_creates_from_set_cookie_strings($cookieStrings, array $expectedCookies) {
		$cookies = (new Cookies())->fromCookieStrings($cookieStrings);

		$this->assertEquals($expectedCookies, $cookies->getAll());
	}

	/**
	 * @param string[] $cookieStrings
	 * @param Cookie[] $expectedCookies
	 *
	 * @test
	 * @dataProvider provideCookieStringsAndExpectedCookiesData
	 */
	public function it_knows_which_set_cookies_are_available($cookieStrings, array $expectedCookies) {
		$cookies = (new Cookies())->fromCookieStrings($cookieStrings);

		foreach ($expectedCookies as $expectedCookie) {
			$this->assertTrue($cookies->has($expectedCookie->getName()));
		}

		$this->assertFalse($cookies->has('i know this cookie does not exist'));
	}

	/**
	 * @test
	 * @dataProvider provideGetsCookieByNameData
	 */
	public function it_gets_set_cookie_by_name($cookieStrings, $CookieName, Cookie $expectedCookie = null) {
		$cookies = (new Cookies())->fromCookieStrings($cookieStrings);

		$this->assertEquals($expectedCookie === null, $cookies->get($CookieName)->isNew());

		if ($expectedCookie !== null)
			$this->assertEquals($expectedCookie, $cookies->get($CookieName));
	}

	/**
	 * @test
	 */
	public function it_renders_added_and_removed_set_cookies_header() {
		$cookies = (new Cookies())->fromCookieStrings(['theme=light', 'sessionToken=abc123', 'hello=world'])
			->add(new Cookie('theme', 'blue'))
			->delete('sessionToken')
			->add(new Cookie('who', 'me'));

		$originalResponse = new FigCookieTestingResponse();
		$response = $cookies->toResponse($originalResponse);

		$this->assertEquals(['theme=blue', 'hello=world', 'who=me'], $response->getHeader(Cookies::SET_COOKIE_HEADER));
		$this->assertEquals(['theme=blue', 'hello=world', 'who=me'], $originalResponse->getHeader(Cookies::SET_COOKIE_HEADER));
	}

	/**
	 * @test
	 */
	public function it_gets_and_updates_set_cookie_value_on_request() {
		//
		// Example of naive cookie encryption middleware.
		//
		// Shows how to access and manipulate cookies using PSR-7 Response
		// instances from outside the Response object itself.
		//

		// Simulate a response coming in with several cookies.
		$response = (new FigCookieTestingResponse())
			->withAddedHeader(Cookies::SET_COOKIE_HEADER, 'theme=light')
			->withAddedHeader(Cookies::SET_COOKIE_HEADER, 'sessionToken=ENCRYPTED')
			->withAddedHeader(Cookies::SET_COOKIE_HEADER, 'hello=world');

		// Get our set cookies from the response.
		$cookies = (new Cookies())->fromResponse($response);

		// Ask for the encrypted session token.
		$decryptedSessionToken = $cookies->get('sessionToken');

		// Get the encrypted value from the cookie and decrypt it.
		$decryptedValue = $decryptedSessionToken->getValue();
		$encryptedValue = str_rot13($decryptedValue);

		// Create a new set cookie with the encrypted value.
		$encryptedSessionToken = $decryptedSessionToken->setValue($encryptedValue);

		// Include our encrypted session token with the rest of our cookies.
		$cookies = $cookies->add($encryptedSessionToken);

		// Render our cookies, along with the newly decrypted session token, into a response.
		$response = $cookies->toResponse($response);

		// From this point on, any response based on this one can get the encrypted version
		// of the session token.
		$this->assertEquals(
			['theme=light', 'sessionToken=RAPELCGRQ', 'hello=world'],
			$response->getHeader(Cookies::SET_COOKIE_HEADER)
		);
	}

	public function provideCookieStringsAndExpectedCookiesData() {
		return [
			[
				[],
				[],
			],
			[
				[
					'someCookie=',
				],
				[
					new Cookie('someCookie'),
				],
			],
			[
				[
					'someCookie=someValue',
					'LSID=DQAAAK%2FEaem_vYg; Path=/accounts; Expires=Wed, 13 Jan 2021 22:23:01 GMT; Secure; HttpOnly',
				],
				[
					new Cookie('someCookie', 'someValue'),
					(new Cookie('LSID'))
						->setValue('DQAAAK/Eaem_vYg')
						->setPath('/accounts')
						->setExpires('Wed, 13 Jan 2021 22:23:01 GMT')
						->setSecure(true)
						->setHttpOnly(true),
				],
			],
			[
				[
					'a=AAA',
					'b=BBB',
					'c=CCC',
				],
				[
					new Cookie('a', 'AAA'),
					new Cookie('b', 'BBB'),
					new Cookie('c', 'CCC'),
				],
			],
		];
	}

	public function provideGetsCookieByNameData() {
		return [
			[
				[
					'a=AAA',
					'b=BBB',
					'c=CCC',
				],
				'b',
				new Cookie('b', 'BBB'),
			],
			[
				[
					'a=AAA',
					'b=BBB',
					'c=CCC',
					'LSID=DQAAAK%2FEaem_vYg; Path=/accounts; Expires=Wed, 13 Jan 2021 22:23:01 GMT; Secure; HttpOnly',
				],
				'LSID',
				(new Cookie('LSID'))
					->setValue('DQAAAK/Eaem_vYg')
					->setPath('/accounts')
					->setExpires('Wed, 13 Jan 2021 22:23:01 GMT')
					->setSecure(true)
					->setHttpOnly(true),
			],
			[
				[
					'a=AAA',
					'b=BBB',
					'c=CCC',
				],
				'LSID',
				null,
			],
		];
	}
}
