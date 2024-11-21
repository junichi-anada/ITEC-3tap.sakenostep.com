<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use App\Contracts\LineMessagingServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Exception;
use Mockery;
use Illuminate\Support\Facades\Config;

class LineWebhookTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用の認証情報を設定
        Config::set('services.line.channel_secret', 'test-channel-secret');
        Config::set('services.line.channel_token', 'test-channel-token');
    }

    /**
     * Webhookエンドポイントのテスト
     */
    public function test_webhook_handles_valid_request(): void
    {
        // LineMessagingServiceInterfaceをモック
        $lineServiceMock = Mockery::mock(LineMessagingServiceInterface::class);
        $lineServiceMock->shouldReceive('handleWebhook')
            ->once()
            ->with(Mockery::type(Request::class))
            ->andReturn(null);

        // モックをコンテナにバインド
        $this->app->instance(LineMessagingServiceInterface::class, $lineServiceMock);

        // テストデータ
        $testEvent = [
            'events' => [
                [
                    'type' => 'message',
                    'message' => [
                        'type' => 'text',
                        'text' => 'Hello, Bot!'
                    ],
                    'replyToken' => 'test-reply-token',
                    'source' => [
                        'userId' => 'test-user-id',
                        'type' => 'user'
                    ]
                ]
            ]
        ];

        // 署名を生成
        $signature = base64_encode(
            hash_hmac('sha256', json_encode($testEvent), Config::get('services.line.channel_secret'), true)
        );

        // リクエストを送信
        $response = $this->postJson('/api/line/webhook', $testEvent, [
            'X-Line-Signature' => $signature
        ]);

        // レスポンスを検証
        $response->assertStatus(204);
    }

    /**
     * 無効な署名でのリクエストをテスト
     */
    public function test_webhook_rejects_invalid_signature(): void
    {
        // LineMessagingServiceInterfaceをモック
        $lineServiceMock = Mockery::mock(LineMessagingServiceInterface::class);
        $lineServiceMock->shouldReceive('handleWebhook')
            ->once()
            ->with(Mockery::type(Request::class))
            ->andThrow(new Exception('Invalid signature'));

        // モックをコンテナにバインド
        $this->app->instance(LineMessagingServiceInterface::class, $lineServiceMock);

        $testEvent = [
            'events' => [
                [
                    'type' => 'message',
                    'message' => [
                        'type' => 'text',
                        'text' => 'Hello, Bot!'
                    ]
                ]
            ]
        ];

        // 無効な署名でリクエストを送信
        $response = $this->postJson('/api/line/webhook', $testEvent, [
            'X-Line-Signature' => 'invalid-signature'
        ]);

        // 401 Unauthorizedが返されることを確認
        $response->assertStatus(401);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
