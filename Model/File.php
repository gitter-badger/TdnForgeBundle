<?php

namespace Tdn\ForgeBundle\Model;

use Symfony\Component\Finder\SplFileInfo;

/**
 * Class File
 * @package Tdn\ForgeBundle\Model
 */
class File extends SplFileInfo
{
    /**
     * @var string
     */
    private $fileLocation;

    /**
     * @var string
     */
    private $content;

    /**
     * @var bool
     */
    private $auxFile;

    /**
     * @var bool
     */
    private $serviceFile;

    /**
     * @param string $file
     * @param string $content
     * @param null $relativePath
     * @param null $relativePathName
     */
    public function __construct($file, $content = null, $relativePath = null, $relativePathName = null)
    {
        $this->fileLocation = $file;
        $this->content = $content;
        $this->auxFile = false;
        $this->serviceFile = false;

        parent::__construct($file, $relativePath, $relativePathName);
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param  bool $auxFile
     * @return $this
     */
    public function setAuxFile($auxFile)
    {
        $this->auxFile = $auxFile;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAuxFile()
    {
        return $this->auxFile;
    }

    /**
     * @param bool $serviceFile
     */
    public function setServiceFile($serviceFile)
    {
        $this->serviceFile = $serviceFile;
    }

    /**
     * @return bool
     */
    public function isServiceFile()
    {
        return $this->serviceFile;
    }

    /**
     * @return bool
     */
    public function isDirty()
    {
        try {
            $currentContents = parent::getContents();
        } catch (\RuntimeException $e) {
            $currentContents = '';
        }

        return ($this->getContent() != $currentContents);
    }

    /**
     * Let's override since parent returns false if the file does not exist.
     *
     * @return string
     */
    public function getRealPath()
    {
        return (parent::getRealPath()) ? parent::getRealPath() : $this->fileLocation;
    }
}
