<?php

namespace PHPCraftdream\FigCookies;

use DateTime;
use DateTimeInterface;

class Cookie implements CookieInterface
{
	use StringUtilTrait;

	protected $isChanged = false;
	protected $name;
	protected $value;
	protected $expires = 0;
	protected $maxAge = 0;
	protected $path;
	protected $domain;
	protected $secure = false;
	protected $httpOnly = false;

	public function __construct($name = NULL, $value = NULL)
	{
		$this->name = $name;
		$this->value = $value;
		$this->isChanged = false;
	}

	protected $isNew = false;
	public function setItNew(): CookieInterface
	{
		$this->isChanged = true;
		$this->isNew = true;
		return $this;
	}

	public function isNew(): bool
	{
		return (bool)$this->isNew;
	}

	public function isChanged(): bool
	{
		return (bool)$this->isChanged;
	}

	public function resetChanged()
	{
		return $this->isChanged = false;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function getExpires()
	{
		return $this->expires;
	}

	public function getMaxAge()
	{
		return $this->maxAge;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getDomain()
	{
		return $this->domain;
	}

	public function getSecure()
	{
		return $this->secure;
	}

	public function getHttpOnly()
	{
		return $this->httpOnly;
	}

	public function setName(string $name = NULL): CookieInterface
	{
		$this->isChanged = true;
		$this->name = $name;

		return $this;
	}

	public function setValue(string $value = NULL): CookieInterface
	{
		$this->isChanged = true;
		$this->value = $value;

		return $this;
	}

	protected function resolveExpires($expires = NULL)
	{
		if (is_null($expires))
			return null;

		if ($expires instanceof DateTimeInterface)
			return $expires->getTimestamp();

		if (is_numeric($expires))
			return $expires;

		return strtotime($expires);
	}

	public function setExpires($expires = NULL): CookieInterface
	{
		$this->isChanged = true;
		$expires = $this->resolveExpires($expires);

		$this->expires = $expires;

		return $this;
	}

	public function rememberForever(): CookieInterface
	{
		return $this->setExpires(new DateTime('+5 years'));
	}

	public function expire(): CookieInterface
	{
		return $this->setExpires(new DateTime('-5 years'));
	}

	public function setMaxAge($maxAge = NULL): CookieInterface
	{
		$this->isChanged = true;
		$this->maxAge = $maxAge;

		return $this;
	}

	public function setPath($path = NULL): CookieInterface
	{
		$this->isChanged = true;
		$this->path = $path;

		return $this;
	}

	public function setDomain($domain = NULL): CookieInterface
	{
		$this->isChanged = true;
		$this->domain = $domain;

		return $this;
	}

	public function setSecure($secure = NULL): CookieInterface
	{
		$this->isChanged = true;
		$this->secure = $secure;

		return $this;
	}

	public function setHttpOnly($httpOnly = NULL): CookieInterface
	{
		$this->isChanged = true;
		$this->httpOnly = $httpOnly;

		return $this;
	}

	public function __toString()
	{
		if ($this->isNew() || $this->isChanged())
		{
			$cookieStringParts = [
				urlencode($this->name) . '=' . urlencode($this->value),
			];

			$cookieStringParts = $this->appendFormattedDomainPartIfSet($cookieStringParts);
			$cookieStringParts = $this->appendFormattedPathPartIfSet($cookieStringParts);
			$cookieStringParts = $this->appendFormattedExpiresPartIfSet($cookieStringParts);
			$cookieStringParts = $this->appendFormattedMaxAgePartIfSet($cookieStringParts);
			$cookieStringParts = $this->appendFormattedSecurePartIfSet($cookieStringParts);
			$cookieStringParts = $this->appendFormattedHttpOnlyPartIfSet($cookieStringParts);

			return implode('; ', $cookieStringParts);
		}

		return '';
	}

	protected function appendFormattedDomainPartIfSet(array $cookieStringParts): array
	{
		if ($this->domain)
			$cookieStringParts[] = sprintf("Domain=%s", $this->domain);

		return $cookieStringParts;
	}

	protected function appendFormattedPathPartIfSet(array $cookieStringParts): array
	{
		if ($this->path)
			$cookieStringParts[] = sprintf("Path=%s", $this->path);

		return $cookieStringParts;
	}

	protected function appendFormattedExpiresPartIfSet(array $cookieStringParts): array
	{
		if ($this->expires)
			$cookieStringParts[] = sprintf("Expires=%s", gmdate('D, d M Y H:i:s T', $this->expires));

		return $cookieStringParts;
	}

	protected function appendFormattedMaxAgePartIfSet(array $cookieStringParts): array
	{
		if ($this->maxAge)
			$cookieStringParts[] = sprintf("Max-Age=%s", $this->maxAge);

		return $cookieStringParts;
	}

	protected function appendFormattedSecurePartIfSet(array $cookieStringParts): array
	{
		if ($this->secure)
			$cookieStringParts[] = 'Secure';

		return $cookieStringParts;
	}

	protected function appendFormattedHttpOnlyPartIfSet(array $cookieStringParts): array
	{
		if ($this->httpOnly)
			$cookieStringParts[] = 'HttpOnly';

		return $cookieStringParts;
	}

	public function parse(string $string): CookieInterface
	{
		$rawAttributes = $this->splitOnAttributeDelimiter($string);
		list($cookieName, $cookieValue) = $this->splitCookiePair(array_shift($rawAttributes));

		if (!is_null($cookieName))
			$this->setName($cookieName);

		if (!is_null($cookieValue))
			$this->setValue($cookieValue);

		while ($rawAttribute = array_shift($rawAttributes))
		{
			$rawAttributePair = explode('=', $rawAttribute, 2);

			$attributeKey = $rawAttributePair[0];
			$attributeValue = count($rawAttributePair) > 1 ? $rawAttributePair[1] : null;

			$attributeKey = strtolower($attributeKey);

			switch ($attributeKey)
			{
				case 'expires':
					$this->setExpires($attributeValue);
				break;

				case 'max-age':
					$this->setMaxAge($attributeValue);
				break;

				case 'domain':
					$this->setDomain($attributeValue);
				break;

				case 'path':
					$this->setPath($attributeValue);
				break;

				case 'secure':
					$this->setSecure(true);
				break;

				case 'httponly':
					$this->setHttpOnly(true);
				break;
			}
		}

		$this->isChanged = false;
		$this->isNew = false;

		return $this;
	}
}
