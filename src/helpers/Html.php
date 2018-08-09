<?php

namespace Itstructure\MFUploader\helpers;

use Itstructure\MFUploader\Module;
use yii\helpers\{ArrayHelper, Html as BaseHtml};

/**
 * HTML helper.
 *
 * @package Itstructure\MFUploader\helpers
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class Html extends BaseHtml
{
    /**
     * Render html 5 audio tag structure.
     *
     * @param string $src
     * @param array $options
     *
     * @return string
     */
    public static function audio(string $src, array $options = []): string
    {
        return self::getTag('audio',
            self::getMainOptions($options),
            self::getSourceOptions($src, $options),
            self::getTrackOptions($options)
        );
    }

    /**
     * Render html 5 video tag structure.
     *
     * @param string $src
     * @param array $options
     *
     * @return string
     */
    public static function video(string $src, array $options = []): string
    {
        return self::getTag('video',
            ArrayHelper::merge([
                'height' => Module::ORIGINAL_PREVIEW_HEIGHT
            ], self::getMainOptions($options)),
            self::getSourceOptions($src, $options),
            self::getTrackOptions($options)
        );
    }

    /**
     * Get main options of the mediafile.
     *
     * @param array $options
     *
     * @return array
     */
    private static function getMainOptions(array $options = []): array
    {
        $mainOptions = [
            'controls' => 'controls',
            'width' => Module::ORIGINAL_PREVIEW_WIDTH,
        ];
        if (isset($options['main']) && is_array($options['main'])) {
            $mainOptions = ArrayHelper::merge($mainOptions, $options['main']);
        }
        return $mainOptions;
    }

    /**
     * Get source options of the mediafile.
     *
     * @param string $src
     * @param array $options
     *
     * @return array
     */
    private static function getSourceOptions(string $src, array $options = []): array
    {
        $sourceOptions = [
            'src' => $src,
            'preload' => 'auto'
        ];
        if (isset($options['source']) && is_array($options['source'])) {
            $sourceOptions = ArrayHelper::merge($sourceOptions, $options['source']);
        }
        return $sourceOptions;
    }

    /**
     * Get track options of the mediafile.
     *
     * @param array $options
     *
     * @return array
     */
    private static function getTrackOptions(array $options = []): array
    {
        $trackOptions = [
            'kind' => 'subtitles'
        ];
        if (isset($options['track']) && is_array($options['track'])) {
            $trackOptions = ArrayHelper::merge($trackOptions, $options['track']);
        }
        return $trackOptions;
    }

    /**
     * Get tag of the mediafile.
     *
     * @param string $tagName
     * @param array $mainOptions
     * @param array $sourceOptions
     * @param array $trackOptions
     *
     * @return string
     */
    private static function getTag(
        string $tagName,
        array $mainOptions,
        array $sourceOptions,
        array $trackOptions): string {

        return static::tag(
            $tagName,
            static::tag('source', '', $sourceOptions) . static::tag('track', '', $trackOptions),
            $mainOptions
        );
    }
}
