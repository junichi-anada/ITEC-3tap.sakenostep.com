<?php

namespace App\Http\Controllers;

use App\Model\LineUser;
use Illuminate\Http\Request;
use App\Services\Messaging\DTOs\LineWebhookData;
use App\Services\Messaging\Actions\PushMessageAction;
use Illuminate\Support\Facades\Log;

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
    /**
     * コンストラクタ
     *
     * @param PushMessageAction $pushMessageAction メッセージ送信アクション
     */
    public function __construct(PushMessageAction $pushMessageAction)
    {
        $this->channelSecret = env('LINE_CHANNEL_SECRET');
        $this->pushMessageAction = $pushMessageAction;
        $this->siteId = env('LINE_SITE_ID');
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

        switch ($eventType) {
            case 'message':
                $this->handleMessageEvent($event, $userId);
                break;
            case 'follow':
                $this->handleFollowEvent($userId);
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
    private function handleMessageEvent(array $event, ?string $userId)
    {
        // メッセージイベントの処理ロジックを実装
        $messageType = $event['message']['type'];
        $messageText = $event['message']['text'] ?? '';

        // LINE連携するメッセージが送信された場合
        if( $messageText == 'LINE連携する') {

            // LineUserモデルを取得
            $lineUser = LineUser::where('line_user_id', $userId)
                ->where('site_id', $this->siteId)
                ->whereNull('deleted_at')
                ->first();

            // Lineユーザーが連携していない場合
            if(is_null($user)){
                $this->accountLinkSend($userId, $replayToken);
                return;
            }

            // 連携済みの場合
            $this->mypageLinkSend($replayToken);
            return;
        }

        // それ以外の場合、定型メッセージを送信
        $message = '世界中のお酒が手に入る！\n十和田市のお酒屋さん「酒のステップ」へようこそ！';
        $this->pushMessageAction->sendMessage($userId, $message);
    }

    /**
     * フォローイベントを処理する
     *
     * @param string|null $userId ユーザーID
     * @return void
     */
    private function handleFollowEvent(?string $userId)
    {
        // サンクスメッセージを送信
        $message = '友達登録ありがとうございます！';
        $this->pushMessageAction->sendMessage($userId, $message);

        // LineUserモデルを取得
        $lineUser = LineUser::where('line_user_id', $userId)->first();

        $lineUser->followed_at = now();
        $lineUser->unfollowed_at = null;
        $lineUser->nonce = null;
        $lineUser->save();

        // LineUserモデルが存在しない場合はアカウント連携用のURLを送信
        if(is_null($lineUser)){
            $this->accountLinkSend($userId, $replayToken);
            return;
        }

        // マイページへのリンクを送信
        $this->mypageLinkSend($replayToken);
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
     * @param string $replayToken
     * @throws \LINE\LINEBot\Exception\CurlExecutionException
     */
    private function accountLinkSend(string $userId, string $replayToken){
        $httpClient = new CurlHTTPClient(config('services.line.message.channel_token'));
        $bot = new LINEBot($httpClient, ['channelSecret' => config('services.line.message.channel_secret')]);
        $response = $bot->createLinkToken($userId);

        $res_json = $response->getJSONDecodedBody();
        $linkToken=$res_json['linkToken'];

        $templateMessage = new TemplateMessageBuilder(
            "LINEと連携ができるようになりました！",
            new ButtonTemplateBuilder(
                "LINE連携ができるようになりました！",
                "3TAPオーダーシステムへのログインは↓のバナーから！！",
                null,
                [
                    new UriTemplateActionBuilder(
                        "連携はこちらから",
                        route("customer.index",["linkToken" => $linkToken])
                    )
                ]
            )
        );
        $response = $bot->replyMessage($replayToken, $templateMessage);
    }

    /**
     * アカウント連携完了メッセージを送信
     *
     * @param string $userId
     * @param string $replayToken
     * @throws \LINE\LINEBot\Exception\CurlExecutionException
     */
    private function accountLinkThanks(string $replayToken){
        $httpClient = new CurlHTTPClient(config('services.line.message.channel_token001'));
        $bot = new LINEBot($httpClient, ['channelSecret' => config('services.line.message.channel_secret001')]);
        $response = $bot->replyText($replayToken, "連携が完了しました。\n今後ともよろしくお願いいたします。\nログインはこちらからお願いします。\n".url('/cst'));
    }

    /**
     * マイページへのリンクを送信
     *
     * @param string $userId
     * @param string $replayToken
     * @throws \LINE\LINEBot\Exception\CurlExecutionException
     */
    private function mypageLinkSend(string $replayToken){
        $httpClient = new CurlHTTPClient(config('services.line.message.channel_token001'));
        $bot = new LINEBot($httpClient, ['channelSecret' => config('services.line.message.channel_secret001')]);
        $response = $bot->replyText($replayToken, "LINE連携は完了しております。\n今後ともよろしくお願いいたします。\nログインはこちらからお願いします。\n".url('/cst'));
    }

}
