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
     * @return string
     */
    public function getAlias(): string ;

    /**
     * Get config name.
     * @return string
     */
    public function getName(): string ;

    /**
     * Get thumb width.
     * @return int
     */
    public function getWidth(): int ;

    /**
     * Get thumb height.
     * @return int
     */
    public function getHeight(): int ;

    /**
     * Get thumb mode.
     * @return string
     */
    public function getMode(): string ;
}
