<?php

namespace Tdn\ForgeBundle\Model;

use Symfony\Component\Finder\SplFileInfo;

/**
 * Class File
 * @package Tdn\ForgeBundle\Model
 */
class File extends SplFileInfo
{
    const QUEUE_DEFAULT = 0;
    const QUEUE_IF_UPGRADE = 1;
    const QUEUE_ALWAYS = 2;

    /**
     * @var string
     */
    private $fileLocation;

    /**
     * @var string
     */
    private $queue;

    /**
     * @var int
     */
    private $queueType;

    public static function getSupportedQueueTypes()
    {
        return [
            self::QUEUE_DEFAULT,
            self::QUEUE_IF_UPGRADE,
            self::QUEUE_ALWAYS
        ];
    }

    /**
     * @param string $file
     * @param string|null $content
     * @param integer|null $queueType
     * @param string|null $relativePath
     * @param string|null $relativePathName
     */
    public function __construct(
        $file,
        $content = null,
        $queueType = self::QUEUE_DEFAULT,
        $relativePath = null,
        $relativePathName = null
    ) {
        if (!is_int($queueType) || !in_array($queueType, self::getSupportedQueueTypes())) {
            throw new \InvalidArgumentException('Invalid write type for file.');
        }

        $this->fileLocation = $file;
        $this->queue = $content;
        $this->queueType = $queueType;

        parent::__construct($file, $relativePath, $relativePathName);
    }

    /**
     * @return string
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * @return int
     */
    public function getQueueType()
    {
        return $this->queueType;
    }

    /**
     * @return bool
     */
    public function isDirty()
    {
        try {
            $currentContents = $this->getContents();
        } catch (\RuntimeException $e) {
            $currentContents = null;
        }

        return ($this->getQueue() !== $currentContents);
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
