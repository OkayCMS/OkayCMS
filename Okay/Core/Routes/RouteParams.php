<?php


namespace Okay\Core\Routes;


class RouteParams
{
    private $slug;

    private $patterns;

    private $defaults;

    public function __construct($slug, $patterns, $defaults)
    {
        $this->slug     = $slug;
        $this->patterns = $patterns;
        $this->defaults = $defaults;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getPatterns()
    {
        return $this->patterns;
    }

    public function getDefaults()
    {
        return $this->defaults;
    }
}