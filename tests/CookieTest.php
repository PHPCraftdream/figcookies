<?php

namespace PHPCraftdream\FigCookies;

class SetCookieTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 * @dataProvider provideParsesFromSetCookieStringData
	 */
	public function it_parses_from_set_cookie_string($cookieString, Cookie $expectedSetCookie)
	{
		$setCookie = (new Cookie())->parse($cookieString);

		$this->assertEquals($expectedSetCookie, $setCookie);
		$this->assertEquals($cookieString, (string) $setCookie);
	}

	public function provideParsesFromSetCookieStringData()
	{
		return [
			[
				'someCookie=',
				(new Cookie('someCookie')),
			],
			[
				'someCookie=someValue',
				(new Cookie('someCookie'))
					->setValue('someValue')
			],
			[
				'LSID=DQAAAK%2FEaem_vYg; Path=/accounts; Expires=Wed, 13 Jan 2021 22:23:01 GMT; Secure; HttpOnly',
				(new Cookie('LSID'))
					->setValue('DQAAAK/Eaem_vYg')
					->setPath('/accounts')
					->setExpires('Wed, 13 Jan 2021 22:23:01 GMT')
					->setSecure(true)
					->setHttpOnly(true)
			],
			[
				'HSID=AYQEVn%2F.DKrdst; Domain=.foo.com; Path=/; Expires=Wed, 13 Jan 2021 22:23:01 GMT; HttpOnly',
				(new Cookie('HSID'))
					->setValue('AYQEVn/.DKrdst')
					->setDomain('.foo.com')
					->setPath('/')
					->setExpires('Wed, 13 Jan 2021 22:23:01 GMT')
					->setHttpOnly(true)
			],
			[
				'SSID=Ap4P%2F.GTEq; Domain=foo.com; Path=/; Expires=Wed, 13 Jan 2021 22:23:01 GMT; Secure; HttpOnly',
				(new Cookie('SSID'))
					->setValue('Ap4P/.GTEq')
					->setDomain('foo.com')
					->setPath('/')
					->setExpires('Wed, 13 Jan 2021 22:23:01 GMT')
					->setSecure(true)
					->setHttpOnly(true)
			],
			[
				'lu=Rg3vHJZnehYLjVg7qi3bZjzg; Domain=.example.com; Path=/; Expires=Tue, 15 Jan 2013 21:47:38 GMT; HttpOnly',
				(new Cookie('lu'))
					->setValue('Rg3vHJZnehYLjVg7qi3bZjzg')
					->setExpires('Tue, 15-Jan-2013 21:47:38 GMT')
					->setPath('/')
					->setDomain('.example.com')
					->setHttpOnly(true)
			],
			[
				'lu=Rg3vHJZnehYLjVg7qi3bZjzg; Domain=.example.com; Path=/; Max-Age=500; Secure; HttpOnly',
				(new Cookie('lu'))
					->setValue('Rg3vHJZnehYLjVg7qi3bZjzg')
					->setMaxAge(500)
					->setPath('/')
					->setDomain('.example.com')
					->setSecure(true)
					->setHttpOnly(true)
			],
			[
				'lu=Rg3vHJZnehYLjVg7qi3bZjzg; Domain=.example.com; Path=/; Expires=Tue, 15 Jan 2013 21:47:38 GMT; Max-Age=500; Secure; HttpOnly',
				(new Cookie('lu'))
					->setValue('Rg3vHJZnehYLjVg7qi3bZjzg')
					->setExpires('Tue, 15-Jan-2013 21:47:38 GMT')
					->setMaxAge(500)
					->setPath('/')
					->setDomain('.example.com')
					->setSecure(true)
					->setHttpOnly(true)
			],
			[
				'lu=Rg3vHJZnehYLjVg7qi3bZjzg; Domain=.example.com; Path=/; Expires=Tue, 15 Jan 2013 21:47:38 GMT; Max-Age=500; Secure; HttpOnly',
				(new Cookie('lu'))
					->setValue('Rg3vHJZnehYLjVg7qi3bZjzg')
					->setExpires(1358286458)
					->setMaxAge(500)
					->setPath('/')
					->setDomain('.example.com')
					->setSecure(true)
					->setHttpOnly(true)
			],
			[
				'lu=Rg3vHJZnehYLjVg7qi3bZjzg; Domain=.example.com; Path=/; Expires=Tue, 15 Jan 2013 21:47:38 GMT; Max-Age=500; Secure; HttpOnly',
				(new Cookie('lu'))
					->setValue('Rg3vHJZnehYLjVg7qi3bZjzg')
					->setExpires(new \DateTime('Tue, 15-Jan-2013 21:47:38 GMT'))
					->setMaxAge(500)
					->setPath('/')
					->setDomain('.example.com')
					->setSecure(true)
					->setHttpOnly(true)
			],
		];
	}

	/**
	 * @test
	 */
	public function it_expires_cookies()
	{
		$setCookie = (new Cookie('expire_immediately'))->setExpires();

		$this->assertLessThan(time(), $setCookie->getExpires());
	}

	/**
	 * @test
	 */
	public function it_creates_long_living_cookies()
	{
		$setCookie = (new Cookie('remember_forever'))->rememberForever();

		$fourYearsFromNow = (new \DateTime('+4 years'))->getTimestamp();
		$this->assertGreaterThan($fourYearsFromNow, $setCookie->getExpires());
	}
}
