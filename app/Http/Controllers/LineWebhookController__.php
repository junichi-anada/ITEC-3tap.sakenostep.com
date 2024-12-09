<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\LineMessagingServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Http\JsonResponse;

final class LineWebhookController extends Controller
{
    public function __construct(
        private readonly LineMessagingServiceInterface $lineMessagingService
    ) {}

    /**
     * LINEからのWebhookリクエストを処理する
     *
     * @param Request $request
     * @return Response|JsonResponse
     */
    public function handle(Request $request): Response|JsonResponse
    {
        try {
            $this->lineMessagingService->handleWebhook($request);
            return response()->noContent();
        } catch (Exception $e) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }
    }
}
