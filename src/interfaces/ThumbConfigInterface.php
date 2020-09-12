<?php

namespace Itstructure\MFUploader\interfaces;

/**
 * Interface ThumbConfigInterface
 *
 * @package Itstructure\MFUploader\interfaces
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
interface ThumbConfigInterface
{
    /**
     * Get alias name.
     *
     * @return string
     */
    public function getAlias(): string ;

    /**
     * Get config name.
     *
     * @return string
     */
    public function getName(): string ;

    /**
     * Get thumb width.
     *
     * @return int|null
     */
    public function getWidth();

    /**
     * Get thumb height.
     *
     * @return int|null
     */
    public function getHeight();

    /**
     * Get thumb mode.
     *
     * @return string
     */
    public function getMode(): string ;
}
