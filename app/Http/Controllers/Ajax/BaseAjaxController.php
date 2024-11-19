<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;

/**
 * Ajax用基底コントローラー
 */
abstract class BaseAjaxController extends BaseController
{
    protected const SUCCESS_MESSAGE = 'success';
    protected const VALIDATION_ERROR_MESSAGE = 'validation_error';
    protected const UNEXPECTED_ERROR_MESSAGE = 'unexpected_error';

    /**
     * 成功レスポンスを返す
     *
     * @param array $data
     * @param int $status
     * @return JsonResponse
     */
    protected function success(array $data = [], int $status = 200): JsonResponse
    {
        return $this->jsonResponse(self::SUCCESS_MESSAGE, $data, $status);
    }

    /**
     * エラーレスポンスを返す
     *
     * @param string $message
     * @param array $errors
     * @param int $status
     * @return JsonResponse
     */
    protected function error(string $message, array $errors = [], int $status = 400): JsonResponse
    {
        return $this->jsonResponse($message, ['errors' => $errors], $status);
    }
}
