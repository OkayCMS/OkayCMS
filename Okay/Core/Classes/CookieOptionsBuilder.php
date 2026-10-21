<?php


namespace Okay\Core\Classes;


class CookieOptionsBuilder
{
    private $options = [];

    public function setTTL(int $seconds): self
    {
        $this->options['ttl'] = $seconds;
        return $this;
    }

    public function setPath(string $path): self
    {
        $this->options['path'] = $path;
        return $this;
    }

    public function setDomain(string $domain): self
    {
        $this->options['domain'] = $domain;
        return $this;
    }

    public function setSecure(bool $secure): self
    {
        $this->options['secure'] = $secure;
        return $this;
    }

    public function setHttpOnly(bool $httpOnly): self
    {
        $this->options['httponly'] = $httpOnly;
        return $this;
    }

    public function setSameSite(string $sameSite): self
    {
        $this->options['samesite'] = $sameSite;
        return $this;
    }

    public function __invoke(array $defaults): array
    {
        return $this->options + $defaults;
    }
}
