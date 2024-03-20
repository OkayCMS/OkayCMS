<?php

namespace Okay\Modules\OkayCMS\Banners\DTO;

class BannerImageLangBackupDTO implements \JsonSerializable
{
    private string $name;
    private string $alt;
    private string $title;
    private string $url;
    private string $description;
    private string $image;
    private string $imageMobile;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getAlt(): string
    {
        return $this->alt;
    }

    /**
     * @param string $alt
     */
    public function setAlt(string $alt): void
    {
        $this->alt = $alt;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    /**
     * @return string
     */
    public function getImageMobile(): string
    {
        return $this->imageMobile;
    }

    /**
     * @param string $imageMobile
     */
    public function setImageMobile(string $imageMobile): void
    {
        $this->imageMobile = $imageMobile;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function fromArray(array $array)
    {
        $this->setName($array['name'] ?? '');
        $this->setAlt($array['alt'] ?? '');
        $this->setTitle($array['title'] ?? '');
        $this->setUrl($array['url'] ?? '');
        $this->setDescription($array['description'] ?? '');
        $this->setImage($array['image'] ?? '');
        $this->setImageMobile($array['imageMobile'] ?? '');
    }
}