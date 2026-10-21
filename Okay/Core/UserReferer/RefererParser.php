<?php


namespace Okay\Core\UserReferer;


class RefererParser
{
    private $referers = [];

    private $internalHosts = [];

    /**
     * @param string|null $configFile шлях до referers.json
     * @param string[] $internalHosts
     */
    public function __construct($configFile = null, array $internalHosts = [])
    {
        if ($configFile === null) {
            $configFile = __DIR__ . '/data/referers.json';
        }

        if (!is_readable($configFile)) {
            throw new \InvalidArgumentException("Referers config file not found: {$configFile}");
        }

        $this->internalHosts = $internalHosts;
        $this->buildIndex(json_decode(file_get_contents($configFile), true));
    }

    /**
     * @param string|null $refererUrl
     * @param string|null $pageUrl
     * @return Referer
     */
    public function parse($refererUrl, $pageUrl = null)
    {
        $refererParts = $this->parseUrl($refererUrl);
        if ($refererParts === null) {
            return Referer::createInvalid();
        }

        $pageUrlParts = $this->parseUrl($pageUrl);

        $isSameHost = $pageUrlParts !== null && $pageUrlParts['host'] === $refererParts['host'];
        if ($isSameHost || in_array($refererParts['host'], $this->internalHosts, true)) {
            return Referer::createInternal();
        }

        $referer = $this->lookup($refererParts['host'], $refererParts['path']);
        if ($referer === null) {
            return Referer::createUnknown();
        }

        $searchTerm = null;
        if (!empty($referer['parameters']) && $refererParts['query'] !== null) {
            parse_str($refererParts['query'], $queryParts);
            foreach ($referer['parameters'] as $parameter) {
                if (isset($queryParts[$parameter])) {
                    $searchTerm = $queryParts[$parameter];
                }
            }
        }

        return Referer::createKnown($referer['medium'], $referer['source'], $searchTerm);
    }

    private function buildIndex($hash)
    {
        if (!is_array($hash)) {
            return;
        }

        foreach ($hash as $medium => $sources) {
            foreach ($sources as $source => $referer) {
                if (empty($referer['domains'])) {
                    continue;
                }

                $parameters = isset($referer['parameters']) ? $referer['parameters'] : [];
                foreach ($referer['domains'] as $domain) {
                    $this->referers[$domain] = [
                        'source'     => $source,
                        'medium'     => $medium,
                        'parameters' => $parameters,
                    ];
                }
            }
        }
    }

    /**
     * @return array|null ['host' => ..., 'path' => ..., 'query' => ...]
     */
    private function parseUrl($url)
    {
        if (empty($url)) {
            return null;
        }

        $parts = parse_url((string) $url);
        if (!is_array($parts) || !isset($parts['scheme'], $parts['host'])) {
            return null;
        }

        if (!in_array(strtolower($parts['scheme']), ['http', 'https'], true)) {
            return null;
        }

        return [
            'host'  => $parts['host'],
            'path'  => isset($parts['path']) ? $parts['path'] : '/',
            'query' => isset($parts['query']) ? $parts['query'] : null,
        ];
    }

    private function lookup($host, $path)
    {
        $referer = $this->lookupPath($host, $path);

        return $referer !== null ? $referer : $this->lookupHost($host);
    }

    private function lookupPath($host, $path)
    {
        while ($path !== '' && $path !== false) {
            $referer = $this->lookupHost($host, $path);
            if ($referer !== null) {
                return $referer;
            }

            $pos = strrpos($path, '/');
            if ($pos === false) {
                break;
            }

            $path = substr($path, 0, $pos);
        }

        return null;
    }

    private function lookupHost($host, $path = null)
    {
        do {
            $key = $host . (string) $path;
            if (isset($this->referers[$key])) {
                return $this->referers[$key];
            }

            $pos = strpos($host, '.');
            if ($pos === false) {
                break;
            }

            $host = substr($host, $pos + 1);
        } while (substr_count($host, '.') > 0);

        return null;
    }
}
