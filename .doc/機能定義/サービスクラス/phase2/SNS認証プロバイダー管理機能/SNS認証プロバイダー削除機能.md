# SNS認証プロバイダー削除機能仕様

## 機能の対象
- 管理者

## 使用テーブル構成
1. **SNS認証プロバイダー情報**
   - **AuthProviderモデル**（AuthProvidersテーブル）
     - フィールド:
       - `provider_code`: ULID形式で一意
       - `name`: プロバイダー名
       - `description`: 説明
       - `is_enable`: 有効フラグ
       - `created_at`: 作成日時
       - `updated_at`: 更新日時
       - `deleted_at`: 削除日時

     - モデルファイル: `AuthProvider.php`
     - マイグレーションファイル: `04_create_auth_providers_table.php`
     - ユニークキー: `provider_code`
     - インデックス: `name`
     - 外部キー: なし

## 実行サービスクラス
- AuthProviderDeleteService

## ビジネスロジック
1. 削除前チェック
   - 対象プロバイダーの存在確認: 指定された`provider_code`がAuthProvidersテーブルに存在することを確認

2. SNS認証プロバイダー情報の論理削除
   - AuthProvidersテーブルの論理削除: `deleted_at`フィールドを設定して論理削除を行う

3. レスポンス生成
   - 削除成功時:
     - HTTPステータスコード: 204 No Content
     - レスポンス: コンテンツなし
   - エラー発生時は適切なステータスコードとメッセージを返却:
     - その他のエラーは500 Internal Server Error

## エラーハンドリング
- 成功:
  - SNS認証プロバイダー情報が正常に削除された場合
    - HTTPステータスコード: 204 No Content
    - レスポンス: なし
- 失敗:
  - その他のエラー
    - HTTPステータスコード: 500 Internal Server Error
    - エラーメッセージ: "SNS認証プロバイダー情報の削除に失敗しました。システム管理者に連絡してください"

## テストケース
1. 正常系
   - 存在するSNS認証プロバイダーを削除
     - 期待結果: 204 No Content

2. 異常系
   - プロバイダー不在エラー
     - 存在しないprovider_codeを指定
       - 期待結果: 404 Not Found、エラーメッセージ: "指定されたプロバイダーが見つかりません" 
