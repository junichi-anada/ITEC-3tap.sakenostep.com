<?php

namespace App\Http\Controllers;

use App\Models\LineUser;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot;
use LINE\Clients\MessagingApi\Model\ButtonsTemplate;
use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;
use LINE\Clients\MessagingApi\Model\TextMessage;
use LINE\Clients\MessagingApi\Model\TemplateMessage;
use LINE\Clients\MessagingApi\Model\URIAction;
use LINE\Constants\MessageType;
use App\Services\Messaging\DTOs\LineWebhookData;
use App\Services\Messaging\LineMessagingService;
use App\Services\Messaging\Actions\PushMessageAction;

/**
 * LINE Webhookを処理するコントローラー
 *
 * @package App\Http\Controllers
 */
class LineWebhookController extends Controller
{
    /**
     * メッセージ文言を定数として定義
     */
    private const LOGIN_GUIDE_MESSAGE = "今後ともよろしくお願いいたします。\nログインはこちらからお願いします。\n";

    private string $channelSecret;
    private int $siteId;
    private PushMessageAction $pushMessageAction;
    private LineMessagingService $lineMessagingService;
    /**
     * コンストラクタ
     *
     * @param PushMessageAction $pushMessageAction メッセージ送信アクション
     * @param LineMessagingService $lineMessagingService LINEメッセージングサービス
     */
    public function __construct(
        PushMessageAction $pushMessageAction, 
        LineMessagingService $lineMessagingService
    ) {
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
                $this->handleAccountLinkEvent($userId, $nonce, $replyToken);
                break;
            case 'unfollow':
                $this->handleUnfollowEvent($userId);
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
     * @param string|null $replyToken リプライトークン
     * @return void
     */
    private function handleMessageEvent(array $event, ?string $userId, ?string $replyToken)
    {
        try {
            $messageType = $event['message']['type'];
            $messageText = $event['message']['text'] ?? '';

            // LINE連携するメッセージが送信された場合
            if ($messageText == '連携する') {
                // LineUserモデルを取得
                $lineUser = LineUser::where('line_user_id', $userId)
                    ->where('site_id', $this->siteId)
                    ->whereNull('deleted_at')
                    ->first();

                if ($lineUser && $lineUser->is_linked) {
                    // 連携済みの場合
                    $this->mypageLinkSend($replyToken);
                    return;
                }

                // 未連携の場合はアカウント連携URLを送信
                $this->accountLinkSend($userId, $replyToken);
                return;
            }

            // それ以外の場合、定型メッセージを送信
            $message = '世界中のお酒が手に入る！十和田市のお酒屋さん「酒のステップ」へようこそ！';
            $this->lineMessagingService->pushMessage($userId, $message);

        } catch (\Exception $e) {
            Log::error('メッセージ処理エラー: ' . $e->getMessage(), [
                'user_id' => $userId,
                'message' => $messageText ?? null,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * フォローイベントを処理する
     *
     * @param string|null $userId ユーザーID
     * @param string|null $replyToken リプライトークン
     * @return void
     */
    private function handleFollowEvent(?string $userId, ?string $replyToken)
    {
        try {
            // サンクスメッセージを送信
            $message = '友達登録ありがとうございます！';
            $this->lineMessagingService->pushMessage($userId, $message);

            // LineUserモデルを取得または作成
            $lineUser = LineUser::where('line_user_id', $userId)
                ->where('site_id', $this->siteId)
                ->whereNull('deleted_at')
                ->first();

            if ($lineUser) {
                // 既存ユーザーの場合、フォロー状態を更新
                $lineUser->update([
                    'followed_at' => now(),
                    'unfollowed_at' => null
                ]);

                // 連携済みの場合はマイページリンクを送信
                if ($lineUser->is_linked) {
                    $this->mypageLinkSend($replyToken);
                    return;
                }
            }

            // 未連携の場合はアカウント連携URLを送信
            $this->accountLinkSend($userId, $replyToken);

        } catch (\Exception $e) {
            Log::error('フォロー処理エラー: ' . $e->getMessage(), [
                'user_id' => $userId,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * アカウントリンクイベントを処理する
     *
     * @param string|null $userId LINEユーザーID
     * @param string|null $nonce ノンタイトークン
     * @param string|null $replyToken リプライトークン
     * @return void
     */
    private function handleAccountLinkEvent(?string $userId, ?string $nonce, ?string $replyToken)
    {
        try {
            // LineUserモデルを取得
            $lineUser = LineUser::where('nonce', $nonce)
                ->where('site_id', $this->siteId)
                ->whereNull('deleted_at')
                ->first();

            if (!$lineUser) {
                Log::error('LINE連携エラー: 無効なnonce', [
                    'nonce' => $nonce,
                    'user_id' => $userId
                ]);
                return;
            }

            // アカウント連携を完了
            if (!$lineUser->completeLink($userId)) {
                throw new \Exception('アカウント連携情報の更新に失敗しました');
            }

            // 連携完了メッセージを送信
            if ($replyToken) {
                $this->accountLinkThanks($replyToken);
            }

        } catch (\Exception $e) {
            Log::error('LINE連携エラー: ' . $e->getMessage(), [
                'user_id' => $userId,
                'nonce' => $nonce,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * フォロー解除イベントを処理する
     *
     * @param string|null $userId ユーザーID
     * @return void
     */
    private function handleUnfollowEvent(?string $userId)
    {
        try {
            $lineUser = LineUser::where('line_user_id', $userId)
                ->where('site_id', $this->siteId)
                ->whereNull('deleted_at')
                ->first();

            if ($lineUser) {
                $lineUser->unlink();
            }

        } catch (\Exception $e) {
            Log::error('フォロー解除処理エラー: ' . $e->getMessage(), [
                'user_id' => $userId,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * アカウント連携用のURLを送信
     *
     * @param string $userId
     * @param string $replyToken
     * @throws \Exception
     */
    private function accountLinkSend(string $userId, string $replyToken)
    {
        try {
            // MessagingApiApiのインスタンスを作成
            $client = new \GuzzleHttp\Client();
            $config = new \LINE\Clients\MessagingApi\Configuration();
            $config->setAccessToken(config('services.line.channel_access_token'));
            $messagingApi = new \LINE\Clients\MessagingApi\Api\MessagingApiApi(
                client: $client,
                config: $config
            );

            // リンクトークンを取得
            $linkToken = $this->lineMessagingService->issueLinkToken($userId);
            if (!$linkToken) {
                throw new \Exception('リンクトークンの取得に失敗しました');
            }

            // ユーザープロフィールを取得
            $profile = $this->lineMessagingService->getProfile($userId);
            if (!$profile) {
                throw new \Exception('ユーザープロフィールの取得に失敗しました');
            }

            // LineUserモデルを作成または更新し、新しいnonceを生成
            $lineUser = LineUser::updateOrCreate(
                [
                    'site_id' => $this->siteId,
                    'line_user_id' => $userId
                ],
                [
                    'is_linked' => false,
                    'display_name' => $profile['displayName'],
                    'picture_url' => $profile['pictureUrl'] ?? null,
                    'status_message' => $profile['statusMessage'] ?? null
                ]
            );

            // 新しいnonceを生成
            $nonce = $lineUser->refreshNonce();

            // アカウント連携URLを生成
            $site = Site::find($this->siteId);
            if (!$site) {
                throw new \Exception('サイト情報が見つかりません');
            }

            $linkUrl = route('line.account.link', [
                'site_code' => $site->site_code,
                'nonce' => $nonce,
                'link_token' => $linkToken
            ], true);

            // テンプレートメッセージを作成
            $templateMessage = new TemplateMessage([
                'type' => MessageType::TEMPLATE,
                'altText' => 'アカウント連携のお願い',
                'template' => [
                    'type' => 'buttons',
                    'text' => "サービスをご利用いただくには、アカウント連携が必要です。\n以下のボタンから連携を行ってください。",
                    'actions' => [
                        [
                            'type' => 'uri',
                            'label' => 'アカウント連携',
                            'uri' => $linkUrl
                        ]
                    ]
                ]
            ]);

            // URLをログファイルに出す
            Log::info('アカウント連携用URL: ' . $linkUrl);

            // URLをメッセージとして送信
            $textMessage = new TextMessage([
                'type' => MessageType::TEXT,
                'text' => "アカウント連携用URL:\n" . $linkUrl
            ]);

            // 両方のメッセージを送信
            $request = new ReplyMessageRequest([
                'replyToken' => $replyToken,
                'messages' => [$templateMessage, $textMessage]
            ]);

            // メッセージ送信時にMessagingApiApiを使用
            $response = $messagingApi->replyMessage($request);
            
            Log::info('アカウント連携メッセージ送信完了', [
                'user_id' => $userId,
                'link_token' => $linkToken,
                'response' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('アカウント連携URL送信エラー: ' . $e->getMessage(), [
                'user_id' => $userId,
                'replyToken' => $replyToken,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * アカウント連携完了メッセージを送信
     *
     * @param string $replyToken リプライトークン
     * @return void
     */
    private function accountLinkThanks(string $replyToken)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $config = new \LINE\Clients\MessagingApi\Configuration();
            // channel_tokenではなくchannel_access_tokenを使用
            $config->setAccessToken(config('services.line.channel_access_token'));
            $messagingApi = new \LINE\Clients\MessagingApi\Api\MessagingApiApi(
                client: $client,
                config: $config
            );

            // メッセージを作成
            $message = new \LINE\Clients\MessagingApi\Model\TextMessage([
                'type' => 'text',
                'text' => "連携が完了しました。\n" . self::LOGIN_GUIDE_MESSAGE . url('/customer')
            ]);

            // リプライメッセージリクエストを作成
            $request = new \LINE\Clients\MessagingApi\Model\ReplyMessageRequest([
                'replyToken' => $replyToken,
                'messages' => [$message]
            ]);

            // メッセージを送信
            $response = $messagingApi->replyMessage($request);
            
            Log::info('連携完了メッセージ送信成功', [
                'reply_token' => $replyToken,
                'response' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('LINE返信エラー: ' . $e->getMessage(), [
                'reply_token' => $replyToken,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * マイページへのリンクを送信
     *
     * @param string $replyToken リプライトークン
     * @return void
     */
    private function mypageLinkSend(string $replyToken)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $config = new \LINE\Clients\MessagingApi\Configuration();
            // channel_tokenではなくchannel_access_tokenを使用
            $config->setAccessToken(config('services.line.channel_access_token'));
            $messagingApi = new \LINE\Clients\MessagingApi\Api\MessagingApiApi(
                client: $client,
                config: $config
            );

            // メッセージを作成
            $message = new \LINE\Clients\MessagingApi\Model\TextMessage([
                'type' => 'text',
                'text' => "LINE連携は完了しております。\n" . self::LOGIN_GUIDE_MESSAGE . url('/customer')
            ]);

            // リプライメッセージリクエストを作成
            $request = new \LINE\Clients\MessagingApi\Model\ReplyMessageRequest([
                'replyToken' => $replyToken,
                'messages' => [$message]
            ]);

            // メッセージを送信
            $response = $messagingApi->replyMessage($request);
            
            Log::info('マイページリンク送信成功', [
                'reply_token' => $replyToken,
                'response' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('LINE返信エラー: ' . $e->getMessage(), [
                'reply_token' => $replyToken,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

}
