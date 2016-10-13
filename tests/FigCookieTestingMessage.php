<?php

namespace PHPCraftdream\FigCookies;

use Psr\Http\Message\StreamInterface;

trait FigCookieTestingMessage
{
    private $headers = [];
    public function getProtocolVersion()
    {
        throw new \RuntimeException("This method has not been implemented.");
    }

    public function withProtocolVersion($version)
    {
        throw new \RuntimeException("This method has not been implemented.");
    }

    public function hasHeader($name)
    {
        throw new \RuntimeException("This method has not been implemented.");
    }

    public function withHeader($name, $value)
    {
        $this->headers[$name] = [$value];

        return $this;
    }

    public function withAddedHeader($name, $value)
    {
        if (!isset($this->headers[$name])) {
            $this->headers[$name] = [];
        }

        $this->headers[$name][] = $value;

        return $this;
    }

    public function withoutHeader($name)
    {
        if (isset($this->headers[$name])) {
            unset($this->headers[$name]);
        }

        return $this;
    }

    public function getBody()
    {
        throw new \RuntimeException("This method has not been implemented.");
    }

    public function withBody(StreamInterface $body)
    {
        throw new \RuntimeException("This method has not been implemented.");
    }

    public function getHeaders()
    {
        throw new \RuntimeException("This method has not been implemented.");
    }

    public function getHeader($name)
    {
        if (!isset($this->headers[$name])) {
            return [];
        }

        return $this->headers[$name];
    }

    public function getHeaderLine($name)
    {
        return implode(',', $this->headers[$name]);
    }

    public function getHeaderLines($name)
    {
        if (!isset($this->headers[$name])) {
            return [];
        }

        return $this->headers[$name];
    }
}
