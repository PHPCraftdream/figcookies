<?php

call_user_func_array(
	function () {
		$ds = DIRECTORY_SEPARATOR;
		$libDir = $ds . 'lib' . $ds;

		$items = array(
			'PHPCraftdream\\FigCookies\\StringUtilTrait' => 'StringUtilTrait.php',
			'PHPCraftdream\\FigCookies\\Cookie' => 'Cookie.php',
			'PHPCraftdream\\FigCookies\\CookieInterface' => 'CookieInterface.php',
			'PHPCraftdream\\FigCookies\\Cookies' => 'Cookies.php',
			'PHPCraftdream\\FigCookies\\CookiesInterface' => 'CookiesInterface.php',
		);

		spl_autoload_register(
			function ($className) use ($items, $libDir) {
				if (strlen($className) < 21) return;

				if (!array_key_exists($className, $items))
					return;

				require_once __DIR__ . $libDir . $items[$className];
			},
			true,
			true
		);
	},
	array()
);