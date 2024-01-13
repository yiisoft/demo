<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Response;

use Yiisoft\DataResponse\DataResponse;
use Yiisoft\Http\Status;

final class ApiResponseDataFactory
{
    public function createFromResponse(DataResponse $response): ApiResponse
    {
        if ($response->getStatusCode() !== Status::OK) {
            return $this
                ->createErrorResponse()
                ->setErrorCode($response->getStatusCode())
                ->setErrorMessage($this->getErrorMessage($response));
        }

        return $this
            ->createSuccessResponse()
            ->setData($response->getData());
    }

    public function createSuccessResponse(): ApiResponse
    {
        return $this
            ->createResponse()
            ->setStatus('success');
    }

    public function createErrorResponse(): ApiResponse
    {
        return $this
            ->createResponse()
            ->setStatus('failed');
    }

    public function createResponse(): ApiResponse
    {
        return new ApiResponse();
    }

    private function getErrorMessage(DataResponse $response): string
    {
        $data = $response->getData();
        if (is_string($data) && !empty($data)) {
            return $data;
        }

        return 'Unknown error';
    }
}
