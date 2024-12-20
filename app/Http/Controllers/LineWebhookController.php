<?php

namespace App\Http\Controllers;

use App\Models\LineUser;
use Illuminate\Http\Request;
use App\Services\Messaging\DTOs\LineWebhookData;

use App\Services\Messaging\LineMessagingService;


use App\Services\Messaging\Actions\PushMessageAction;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\Clients\MessagingApi\Model\ButtonsTemplate;
use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;
use LINE\Clients\MessagingApi\Model\TextMessage;
use LINE\Clients\MessagingApi\Model\TemplateMessage;
use LINE\Clients\MessagingApi\Model\URIAction;
use LINE\Constants\ActionType;
use LINE\Constants\MessageType;
use LINE\Constants\TemplateType;

/**
 * LINE Webhookを処理するコントローラー
 *
 * @package App\Http\Controllers
 */
class LineWebhookController extends Controller
{
    private $channelSecret;
    private $pushMessageAction;
    private $siteId;
    private $lineMessagingService;
    /**
     * コンストラクタ
     *
     * @param PushMessageAction $pushMessageAction メッセージ送信アクション
     */
    public function __construct(PushMessageAction $pushMessageAction, LineMessagingService $lineMessagingService)
    {
        $this->channelSecret = config('services.line.channel_secret');
        $this->pushMessageAction = $pushMessageAction;
        $this->siteId = config('services.line.site_id');
        $this->lineMessagingService = $lineMessagingService;
    }

    /**
     * Webhookリクエストを処理する
     *
     * @param Request $request HTTPリクエスト
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request)
    {
        // LineWebhookDataオブジェクトを作成
        $webhookData = LineWebhookData::fromRequest($request);

        // 署名の検証
        if (!$this->isValidSignature($webhookData->signature, $webhookData->content)) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        // イベントごとに処理を実行
        foreach ($webhookData->events as $event) {
            $this->handleEvent($event);
        }

        return response()->json(['message' => 'OK'], 200);
    }

    /**
     * 署名を検証する
     *
     * @param string $signature 署名
     * @param string $content コンテンツ
     * @return bool
     */
    private function isValidSignature($signature, $content)
    {
        $hash = hash_hmac('sha256', $content, $this->channelSecret, true);
        $expectedSignature = base64_encode($hash);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * イベントを処理する
     *
     * @param array $event イベントデータ
     * @return void
     */
    private function handleEvent(array $event)
    {
        $eventType = LineWebhookData::getEventType($event);
        $userId = LineWebhookData::getUserId($event);
        $replyToken = LineWebhookData::getReplyToken($event);

        switch ($eventType) {
            case 'message':
                $this->handleMessageEvent($event, $userId, $replyToken);
                break;
            case 'follow':
                $this->handleFollowEvent($userId, $replyToken);
                break;
            case 'accountLink':
                $nonce = LineWebhookData::getNonce($event);
                $this->handleAccountLinkEvent($userId, $nonce);
                break;
            case 'unfollow':
                $nonce = LineWebhookData::getNonce($event);
                $this->handleUnfollowEvent($userId, $nonce);
                break;
            default:
                Log::info('Unhandled event type: ' . $eventType);
                break;
        }
    }

    /**
     * メッセージイベントを処理する
     *
     * @param array $event イベントデータ
     * @param string|null $userId ユーザーID
     * @return void
     */
    private function handleMessageEvent(array $event, ?string $userId, ?string $replyToken)
    {
        // メッセージイベントの処理ロジックを実装
        $messageType = $event['message']['type'];
        $messageText = $event['message']['text'] ?? '';

        // LINE連携するメッセージが送信された場合
        if( $messageText == '連携する') {

            // LineUserモデルを取得
            $lineUser = LineUser::where('line_user_id', $userId)
                ->where('site_id', $this->siteId)
                ->whereNull('deleted_at')
                ->first();

            // Lineユーザーが連携していない場合
            if(is_null($lineUser)){
                $this->accountLinkSend($userId, $replyToken);
                return;
            }

            // 連携済みの場合
            $this->mypageLinkSend($replyToken);
            return;
        }

        // それ以外の場合、定型メッセージを送信
        $message = '世界中のお酒が手に入る！十和田市のお酒屋さん「酒のステップ」へようこそ！';
        // $this->pushMessageAction->sendMessage($userId, $message);
        $this->lineMessagingService->pushMessage($userId, $message);
    }

    /**
     * フォローイベントを処理する
     *
     * @param string|null $userId ユーザーID
     * @return void
     */
    private function handleFollowEvent(?string $userId, ?string $replyToken)
    {
        // サンクスメッセージを送信
        $message = '友達登録ありがとうございます！';
        // $this->pushMessageAction->sendMessage($userId, $message);
        $this->lineMessagingService->pushMessage($userId, $message);

        // LineUserモデルを取得
        $lineUser = LineUser::where('line_user_id', $userId)->first();

        $lineUser->followed_at = now();
        $lineUser->unfollowed_at = null;
        $lineUser->nonce = null;
        $lineUser->save();

        // LineUserモデルが存在しない場合はアカウント連携用のURLを送信
        if(is_null($lineUser)){
            $this->accountLinkSend($userId, $replyToken);
            return;
        }

        // マイページへのリンクを送信
        $this->mypageLinkSend($replyToken);
        return;
    }

    /**
     * アカウント連携イベントを処理する
     *
     * @param string|null $userId ユーザーID
     * @param string|null $nonce ノンス
     * @return void
     */
    private function handleAccountLinkEvent(?string $userId, ?string $nonce)
    {
        // LineUserモデルを取得
        $lineUser = LineUser::where('nonce', $nonce)->first();

        // LineUserモデルがある場合、アカウントリンク成立フラグを立てる
        if(!is_null($lineUser)){
            $lineUser->user_id = $userId;
            $lineUser->is_linked = true;
            $lineUser->nonce = null;
            $lineUser->save();
        }
    }

    /**
     * フォロー解除イベントを処理する
     *
     * @param string|null $userId ユーザーID
     * @return void
     */
    private function handleUnfollowEvent(?string $userId, ?string $nonce)
    {
        // LineUserモデルを取得
        $lineUser = LineUser::where('line_user_id', $userId)->first();

        // LineUserモデルがある場合、フォロー解除フラグを立てる
        if(!is_null($lineUser)){
            $lineUser->is_linked = false;
            $lineUser->followed_at = null;
            $lineUser->unfollowed_at = now();
            $lineUser->save();
        }
    }

    /**
     * アカウント連携用のURLを送信
     *
     * @param string $userId
     * @param string $replyToken
     * @throws \LINE\LINEBot\Exception\CurlExecutionException
     */
    private function accountLinkSend(string $userId, string $replyToken)
    {
        $linkToken = $this->lineMessagingService->getLinkToken($userId);

        $client = new \GuzzleHttp\Client();
        $config = new \LINE\Clients\MessagingApi\Configuration();
        $config->setAccessToken(config('services.line.channel_token'));
        $messagingApi = new \LINE\Clients\MessagingApi\Api\MessagingApiApi(
            client: $client,
            config: $config,
        );

        $templateMessage = new TemplateMessage([
            'type' => MessageType::TEMPLATE,
            'altText' => 'LINEと連携ができるようになりました！',
            'template' => new ButtonsTemplate([
                'type' => TemplateType::BUTTONS,
                'title' => "LINE連携ができるようになりました！",
                'text' => "3TAPオーダーシステムへのログインは↓のリンクから！！",
                'thumbnailImageUrl' => null,
                'actions' => [
                    new URIAction([
                        'type' => ActionType::URI,
                        'label' => '連携はこちらから',
                        'uri' => route("customer.index",["linkToken" => $linkToken]),
                    ])
                ]
            ])
        ]);
        $request = new ReplyMessageRequest([
            'replyToken' => $replyToken,
            'messages' => [$templateMessage],
        ]);
        $response = $messagingApi->replyMessage($request);
    }

    /**
     * アカウント連携完了メッセージを送信
     *
     * @param string $userId
     * @param string $replyToken
     * @throws \LINE\LINEBot\Exception\CurlExecutionException
     */
    private function accountLinkThanks(string $replyToken){
        $httpClient = new CurlHTTPClient(config('services.line.channel_token'));
        $bot = new LINEBot($httpClient, ['channelSecret' => config('services.line.channel_secret001')]);
        $response = $bot->replyText($replyToken, "連携が完了しました。\n今後ともよろしくお願いいたします。\nログインはこちらからお願いします。\n".url('/login'));
    }

    /**
     * マイページへのリンクを送信
     *
     * @param string $userId
     * @param string $replyToken
     * @throws \LINE\LINEBot\Exception\CurlExecutionException
     */
    private function mypageLinkSend(string $replyToken){
        $httpClient = new CurlHTTPClient(config('services.line.channel_token'));
        $bot = new LINEBot($httpClient, ['channelSecret' => config('services.line.channel_secret')]);
        $response = $bot->replyText($replyToken, "LINE連携は完了しております。\n今後ともよろしくお願いいたします。\nログインはこちらからお願いします。\n".url('/login'));
    }

}
