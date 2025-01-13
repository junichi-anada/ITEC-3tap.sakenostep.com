# LINE連携機能設計書

## **1. 機能概要**
- **目的**: LINEアカウントと自社サービスのアカウントを連携する機能
- **対象者**: LINEを利用している顧客
- **方式**: LINE Messaging APIとWebhookを利用した連携方式

## **2. 要件一覧**
### **必須要件**
- LINE Messaging APIを利用したアカウント連携
- 連携トークンの発行と管理
- Webhookによるイベント受信
- セキュアな認証処理の実装
- 連携状態の管理

### **拡張要件**
- 連携解除機能
- 連携状態の確認機能
- 連携履歴の管理機能

## **3. 入出力仕様**
### **入力**
| 項目         | 型     | 必須 | 説明                    |
|--------------|--------|------|-------------------------|
| `lineUserId` | string | 必須 | LINEユーザーID          |
| `nonce`      | string | 必須 | 連携時の一時トークン     |
| `_token`     | string | 必須 | CSRFトークン            |

### **出力**
#### **正常系**
- **HTTPステータス**: 200 OK
- **レスポンス例**:
```json
{
    "status": "success",
    "message": "アカウント連携が完了しました",
    "data": {
        "linkToken": "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx",
        "expiresIn": 3600
    }
}
```

#### **異常系**
- **認証エラー**:
  - HTTPステータス: 401 Unauthorized
```json
{
    "status": "error",
    "message": "認証に失敗しました",
    "errors": {
        "auth": ["認証情報が無効です"]
    }
}
```

## **4. 処理フロー**
1. 連携トークン発行処理
   - LINEユーザーIDの検証
   - 連携トークンの生成
   - トークンの一時保存

2. 連携URL送信
   - Messaging APIを使用したURL送信
   - トークンの有効期限設定

3. ユーザー認証
   - ログイン画面表示
   - 認証情報の検証
   - nonceの生成

4. アカウント連携
   - Webhookイベントの受信
   - nonceの検証
   - アカウント情報の紐付け

## **5. データ構造**
### **line_accounts テーブル**
| カラム名        | 型          | 制約        | 説明                    |
|----------------|-------------|-------------|-------------------------|
| `id`           | INT         | PK, AI      | LINEアカウントID        |
| `line_user_id` | VARCHAR(64) | UK, NOT NULL| LINEユーザーID          |
| `display_name` | VARCHAR(64) |             | LINE表示名              |
| `access_token` | VARCHAR(255)|             | アクセストークン         |
| `refresh_token`| VARCHAR(255)|             | リフレッシュトークン     |
| `token_expiry` | DATETIME    |             | トークン有効期限         |
| `created_at`   | DATETIME    | NOT NULL    | 作成日時                |
| `updated_at`   | DATETIME    |             | 更新日時                |

### **authenticate_line_links テーブル**
| カラム名           | 型          | 制約        | 説明                    |
|-------------------|-------------|-------------|-------------------------|
| `id`              | INT         | PK, AI      | リンクID                |
| `authenticate_id` | INT         | FK, NOT NULL| 認証レコードID          |
| `line_account_id` | INT         | FK, NOT NULL| LINEアカウントID        |
| `nonce`           | VARCHAR(128)| NOT NULL    | 連携用のワンタイムトークン|
| `link_token`      | VARCHAR(255)|             | プロバイダ発行の連携トークン|
| `linked_at`       | DATETIME    |             | 連携日時                |
| `unlink_at`       | DATETIME    |             | 連携解除日時            |
| `created_at`      | DATETIME    | NOT NULL    | 作成日時                |
| `updated_at`      | DATETIME    |             | 更新日時                |

## **6. バリデーション仕様**
### トークンバリデーション
- 連携トークン: 有効期限60分
- nonce: 1回限りの使用
- CSRF対策の実施

### データバリデーション
- `lineUserId`: 必須、LINEの形式に準拠
- `nonce`: 必須、UUID形式
- `userId`: 必須、既存ユーザーの存在確認

## **7. エラーハンドリング**
### 認証エラー
- 認証失敗: 401 Unauthorized
- トークン期限切れ: 401 Unauthorized
- nonce無効: 400 Bad Request

### システムエラー
- API通信エラー: 503 Service Unavailable
- DB登録エラー: トランザクションロールバック

## **8. セキュリティ対策**
- HTTPS通信の強制
- トークンの有効期限管理
- nonceによる重複連携防止
- CSRFトークンによる保護
- アクセス権限の確認

## **9. パフォーマンス対策**
- インデックスの適切な設定
- キャッシュの活用
- 非同期処理の採用
- コネクションプールの活用

## **10. テストケース**
### 正常系
1. 正常なアカウント連携フロー
2. 有効な認証情報での連携
3. Webhook受信後の処理

### 異常系
1. 無効なトークンでの連携試行
2. 期限切れトークンの使用
3. 不正なnonceでの連携試行
4. 重複連携の試行
