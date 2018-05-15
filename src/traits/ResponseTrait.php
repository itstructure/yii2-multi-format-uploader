<?php

namespace Itstructure\MFUploader\traits;

/**
 * Trait ResponseTrait
 *
 * @package Itstructure\MFUploader\traits
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
trait ResponseTrait
{
    /**
     * Returns success response.
     * @param string     $message
     * @param array|null $data
     * @return array
     */
    protected function getSuccessResponse(string $message, array $data = null): array
    {
        return $this->getStatusResponse($message, 'success', $data);
    }

    /**
     * Returns fail response.
     * @param string     $message
     * @param array|null $data
     * @return array
     */
    protected function getFailResponse(string $message, array $data = null): array
    {
        return $this->getStatusResponse($message, 'fail', $data);
    }

    /**
     * Returns status, message and code.
     * @param string     $message
     * @param string     $status
     * @param int        $statusCode
     * @param array|null $data
     * @return array
     */
    protected function getStatusResponse(string $message, string $status, array $data = null, int $statusCode = 200): array
    {
        if (null === $data) {
            $data = (object)[];
        }

        \Yii::$app->response->statusCode = $statusCode;

        return [
            'meta' => [
                'status' => $status,
                'message' => $message,
            ],
            'data' => $data,
        ];
    }
}