<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\LineMessagingService;
use LINE\LINEBot;
use LINE\LINEBot\Response;
use Mockery;
use Illuminate\Support\Facades\Config;

class LineMessagingServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.line.channel_token', 'test-token');
        Config::set('services.line.channel_secret', 'test-secret');
        Config::set('services.line.provider_id', 1);
        Config::set('services.line.site_id', 1);
    }

    /**
     * プッシュメッセージのテスト
     */
    public function test_push_message_success(): void
    {
        // LINEBotのモックを作成
        $botMock = Mockery::mock(LINEBot::class);
        $botMock->shouldReceive('pushMessage')
            ->once()
            ->andReturn(new Response(200, '{"message":"ok"}'));

        // LineMessagingServiceのインスタンスを作成し、モックのbotをセット
        $lineService = new LineMessagingService();
        $reflection = new \ReflectionClass($lineService);
        $property = $reflection->getProperty('bot');
        $property->setAccessible(true);
        $property->setValue($lineService, $botMock);

        // テスト実行
        $result = $lineService->pushMessage('test-user-id', 'テストメッセージ');

        // 結果を検証
        $this->assertTrue($result);
    }

    /**
     * プッシュメッセージ失敗のテスト
     */
    public function test_push_message_failure(): void
    {
        // LINEBotのモックを作成（エラーレスポンスを返す）
        $botMock = Mockery::mock(LINEBot::class);
        $botMock->shouldReceive('pushMessage')
            ->once()
            ->andReturn(new Response(400, '{"message":"error"}'));

        // LineMessagingServiceのインスタンスを作成し、モックのbotをセット
        $lineService = new LineMessagingService();
        $reflection = new \ReflectionClass($lineService);
        $property = $reflection->getProperty('bot');
        $property->setAccessible(true);
        $property->setValue($lineService, $botMock);

        // テスト実行
        $result = $lineService->pushMessage('test-user-id', 'テストメッセージ');

        // 結果を検証
        $this->assertFalse($result);
    }

    /**
     * 一斉送信のテスト
     */
    public function test_multicast_success(): void
    {
        // LINEBotのモックを作成
        $botMock = Mockery::mock(LINEBot::class);
        $botMock->shouldReceive('multicast')
            ->once()
            ->andReturn(new Response(200, '{"message":"ok"}'));

        // LineMessagingServiceのインスタンスを作成し、モックのbotをセット
        $lineService = new LineMessagingService();
        $reflection = new \ReflectionClass($lineService);
        $property = $reflection->getProperty('bot');
        $property->setAccessible(true);
        $property->setValue($lineService, $botMock);

        // テスト実行
        $result = $lineService->multicast(['user-1', 'user-2'], 'テストメッセージ');

        // 結果を検証
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
