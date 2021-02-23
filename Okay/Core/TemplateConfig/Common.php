<?php


namespace Okay\Core\TemplateConfig;


class Common
{

    protected $filename;
    protected $position = 'head';
    protected $dir;
    protected $individual = false;
    protected $preload = false;

    /**
     * Т.к. параметр $filename является единственным обязательным, мы его принимаем в конструктор,
     * чтобы небыло шанса создать объект без него. 
     * @param mixed $filename
     * @throws \Exception
     */
    public function __construct($filename)
    {
        if (empty($filename)) {
            throw new \Exception("Filename cannot be empty");
        }
        $this->filename = $filename;
    }

    /**
     * Установка директории скрипта, относительно корня сайта.
     * Если скрипт находится в теме (директория js или css соответственно), директорию можно не указывать.
     * @param mixed $dir
     * @return $this
     * @throws \Exception
     */
    public function setDir($dir)
    {
        if (!is_dir($dir)) {
            throw new \Exception("Dir \"{$dir}\" not exists");
        }

        $this->dir = rtrim($dir, '/') . '/';
        return $this;
    }
    
    /**
     * @param string $position (head|footer)
     * @return $this
     * @throws \Exception
     */
    public function setPosition($position)
    {
        if (!in_array($position, ['head', 'footer'])) {
            throw new \Exception("Position \"{$position}\" is wrong. Need use \"head\" or \"footer\"");
        }

        $this->position = $position;
        return $this;
    }

    /**
     * Установка флага что нужно добавить link rel="preload"
     * @return $this
     */
    public function preload()
    {
        $this->preload = true;
        return $this;
    }
    
    /**
     * Установка флага что файл должен подключиться индивидуально, не в общем скомпилированном файле
     * true - подключаем индивидуально, false - файл будет подключен в общем скомпилированном файле
     * @param bool $individual
     * @return $this
     */
    public function setIndividual($individual)
    {
        $this->individual = $individual;
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function getPreload()
    {
        return $this->preload;
    }

    /**
     * @return mixed
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * @return mixed
     */
    public function getIndividual()
    {
        return $this->individual;
    }
    
}