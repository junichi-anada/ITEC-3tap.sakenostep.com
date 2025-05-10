<?php

namespace Tests\Unit\App\Http\Controllers\Ajax\Customer;

use Tests\TestCase;
use App\Http\Controllers\Ajax\Customer\OrderController;
use App\Services\Messaging\LineMessagingService;
use App\Services\Order\OrderDetailService;
use App\Services\Order\OrderService;
use App\Services\Item\ItemService;
use App\Models\User;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Mockery;

class OrderControllerTest extends TestCase
{
    protected $lineMessagingServiceMock;
    protected $orderDetailServiceMock;
    protected $orderServiceMock;
    protected $itemServiceMock;
    protected $controller;
    protected $userMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->lineMessagingServiceMock = Mockery::mock(LineMessagingService::class);
        $this->orderDetailServiceMock = Mockery::mock(OrderDetailService::class);
        $this->orderServiceMock = Mockery::mock(OrderService::class);
        $this->itemServiceMock = Mockery::mock(ItemService::class);

        $this->userMock = Mockery::mock(User::class)->makePartial();
        $this->userMock->id = 1;
        $this->userMock->site_id = 1;
        // line_user_id は各テストケースで設定します。

        $this->controller = new OrderController(
            $this->orderDetailServiceMock,
            $this->orderServiceMock,
            $this->itemServiceMock,
            $this->lineMessagingServiceMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     * LINE通知が有効な場合、カート追加時にLINEメッセージが送信されること
     */
    public function store_sendsLineMessage_whenFeatureFlagIsEnabledAndUserHasLineId()
    {
        Config::set('features.enable_line_notification', true);
        $this->userMock->line_user_id = 'U_VALID_LINE_ID';

        $request = new Request(['item_code' => 'TEST_ITEM_001', 'volume' => 1]);
        $this->actingAs($this->userMock); // 認証ユーザーを設定

        $mockOrderDetail = Mockery::mock(OrderDetail::class);
        $mockOrderDetail->order_code = 'ORDER_TEST_001';
        $mockOrderDetail->total_price = 1000;
        $mockOrderDetail->detail_code = 'DETAIL_TEST_001';

        $this->orderDetailServiceMock
            ->shouldReceive('addOrderDetail')
            ->once()
            ->andReturn($mockOrderDetail);

        $this->lineMessagingServiceMock
            ->shouldReceive('pushMessage')
            ->once()
            ->with($this->userMock->line_user_id, Mockery::type('string'))
            ->andReturn(true);

        $response = $this->controller->store($request);
        $response->assertStatus(200);
    }

    /**
     * @test
     * LINE通知が無効な場合、カート追加時にLINEメッセージが送信されないこと
     */
    public function store_doesNotSendLineMessage_whenFeatureFlagIsDisabled()
    {
        Config::set('features.enable_line_notification', false);
        $this->userMock->line_user_id = 'U_VALID_LINE_ID';

        $request = new Request(['item_code' => 'TEST_ITEM_002', 'volume' => 1]);
        $this->actingAs($this->userMock);

        $mockOrderDetail = Mockery::mock(OrderDetail::class);
        $mockOrderDetail->detail_code = 'DETAIL_TEST_002';


        $this->orderDetailServiceMock
            ->shouldReceive('addOrderDetail')
            ->once()
            ->andReturn($mockOrderDetail);

        $this->lineMessagingServiceMock
            ->shouldNotReceive('pushMessage');

        $response = $this->controller->store($request);
        $response->assertStatus(200);
    }

    /**
     * @test
     * LINEユーザーIDがない場合、フィーチャーフラグが有効でもLINEメッセージが送信されないこと
     */
    public function store_doesNotSendLineMessage_whenUserHasNoLineIdEvenIfFlagIsEnabled()
    {
        Config::set('features.enable_line_notification', true);
        $this->userMock->line_user_id = null; // LINE IDなし

        $request = new Request(['item_code' => 'TEST_ITEM_003', 'volume' => 1]);
        $this->actingAs($this->userMock);

        $mockOrderDetail = Mockery::mock(OrderDetail::class);
        $mockOrderDetail->detail_code = 'DETAIL_TEST_003';

        $this->orderDetailServiceMock
            ->shouldReceive('addOrderDetail')
            ->once()
            ->andReturn($mockOrderDetail);

        $this->lineMessagingServiceMock
            ->shouldNotReceive('pushMessage');

        $response = $this->controller->store($request);
        $response->assertStatus(200);
    }
}
