<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\LineUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LineAccountLinkController extends Controller
{
    /**
     * アカウント連携フォームを表示
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showLinkForm(Request $request)
    {
        try {
            // パラメータの検証
            $siteCode = $request->query('site_code');
            $nonce = $request->query('nonce');
            $linkToken = $request->query('link_token');

            if (!$siteCode || !$nonce || !$linkToken) {
                throw new \Exception('必要なパラメータが不足しています');
            }

            // サイトの取得
            $site = Site::where('site_code', $siteCode)->firstOrFail();
            
            // LineUserの検証
            $lineUser = LineUser::where('nonce', $nonce)
                ->where('site_id', $site->id)
                ->whereNull('deleted_at')
                ->firstOrFail();

            return view('customer.line.account.link-form', [
                'site' => $site,
                'nonce' => $nonce,
                'link_token' => $linkToken
            ]);

        } catch (\Exception $e) {
            Log::error('アカウント連携フォーム表示エラー: ' . $e->getMessage());
            return redirect()->route('line.account.error')
                ->with('error', 'アカウント連携に失敗しました');
        }
    }

    /**
     * アカウント連携処理を実行
     *
     * @param string $site_code
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processLink($site_code, Request $request)
    {
        try {
            Log::info('アカウント連携処理開始', [
                'site_code' => $site_code,
                'request' => $request->all()
            ]);

            // サイト情報の取得
            $site = Site::where('site_code', $site_code)->firstOrFail();

            // パラメータの検証
            $nonce = $request->input('nonce');
            $linkToken = $request->input('link_token');
            if (!$nonce || !$linkToken) {
                throw new \Exception('必要なパラメータが不足しています');
            }

            // LineUserの取得と検証
            $lineUser = LineUser::where('nonce', $nonce)
                ->where('site_id', $site->id)
                ->whereNull('deleted_at')
                ->firstOrFail();

            // ユーザー認証
            if (!Auth::attempt([
                'login_code' => $request->input('login_code'),
                'password' => $request->input('password'),
                'site_id' => $site->id
            ])) {
                throw new \Exception('認証に失敗しました');
            }

            // 認証出来たらline_usersのuser_idを更新
            $lineUser->user_id = auth()->user()->id;
            $lineUser->save();

            // LINEのアカウント連携画面にリダイレクト
            return redirect()->away(
                "https://access.line.me/dialog/bot/accountLink?linkToken={$linkToken}&nonce={$nonce}"
            );

        } catch (\Exception $e) {
            Log::error('アカウント連携処理エラー: ' . $e->getMessage(), [
                'site_code' => $site_code,
                'request' => $request->all()
            ]);
            return redirect()->route('line.account.error')
                ->with('error', 'アカウント連携に失敗しました');
        }
    }
}
