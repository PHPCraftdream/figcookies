<?php

namespace PHPCraftdream\FigCookies
{
	trait StringUtilTrait
	{
		public function splitOnAttributeDelimiter(string $string): array
		{
			return array_filter(preg_split('@\s*[;]\s*@', $string));
		}

		public function splitCookiePair(string $string): array
		{
			$pairParts = explode('=', $string, 2);

			if (count($pairParts) === 1)
				$pairParts[1] = '';

			return array_map('urldecode', $pairParts);
		}
	}
}
