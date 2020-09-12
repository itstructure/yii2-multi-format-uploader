<?php

namespace Itstructure\MFUploader\components;

use Itstructure\MFUploader\interfaces\ThumbConfigInterface;

/**
 * Class ThumbConfig
 *
 * @property string $alias Alias name.
 * @property string $name Config name.
 * @property int $width Thumb width.
 * @property int $height Thumb height.
 * @property string $mode Thumb mode.
 *
 * @package Itstructure\MFUploader\components
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class ThumbConfig implements ThumbConfigInterface
{
    /**
     * Alias name.
     *
     * @var string
     */
    public $alias;

    /**
     * Config name.
     *
     * @var string
     */
    public $name;

    /**
     * Thumb width.
     *
     * @var int|null
     */
    public $width;

    /**
     * Thumb height.
     *
     * @var int|null
     */
    public $height;

    /**
     * Thumb mode.
     *
     * @var
     */
    public $mode;

    /**
     * Get alias name.
     *
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * Get config name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get thumb width.
     *
     * @return int|null
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Get thumb height.
     *
     * @return int|null
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Get thumb mode.
     *
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }
}
