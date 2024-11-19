# SNS認証プロバイダー更新機能仕様

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

## バリデーションルール
- **AuthProviderテーブル**
  1. `provider_code`: 必須, ユニーク, ULIDを使用
  2. `name`: 必須, 32文字以内
  3. `description`: 任意, 255文字以内
  4. `is_enable`: 必須, 真偽値

## 実行サービスクラス
- AuthProviderUpdateService

## ビジネスロジック
1. 更新前チェック
   - 対象プロバイダーの存在確認: 指定された`provider_code`がAuthProvidersテーブルに存在することを確認

2. リクエストデータのバリデーション
   - 必須項目の確認: `provider_code`, `name`, `is_enable`が含まれていることを確認
   - 文字数制限の確認:
     - `name`は32文字以内
     - `description`は255文字以内（任意）

3. SNS認証プロバイダー情報の更新
   - AuthProvidersテーブルの更新:
     - `name`, `description`, `is_enable`を更新

4. レスポンス生成
   - 更新されたSNS認証プロバイダー情報を返却: `provider_code`, `name`, `description`, `is_enable`を含む
   - エラー発生時は適切なステータスコードとメッセージを返却:
     - バリデーションエラーの場合は422 Unprocessable Entity
     - その他のエラーは500 Internal Server Error

## エラーハンドリング
- 成功:
  - SNS認証プロバイダー情報が正常に更新された場合
    - HTTPステータスコード: 200 OK
    - レスポンス: 更新されたSNS認証プロバイダー情報（provider_code, name, description, is_enable）を返却
- 失敗:
  - バリデーションエラー
    - HTTPステータスコード: 422 Unprocessable Entity
    - エラーメッセージ: 各項目のバリデーションエラー内容を返却
  - その他のエラー
    - HTTPステータスコード: 500 Internal Server Error
    - エラーメッセージ: "SNS認証プロバイダー情報の更新に失敗しました。システム管理者に連絡してください"

## テストケース
1. 正常系
   - 一部項目のみ更新
     - 期待結果: 200 OK、更新されたSNS認証プロバイダー情報が返却される
   - 全項目を更新
     - 期待結果: 200 OK、更新されたSNS認証プロバイダー情報が返却される

2. 異常系
   - バリデーションエラー
     - nameが32文字を超える
       - 期待結果: 422 Unprocessable Entity、エラーメッセージ: "nameは32文字以内で入力してください" 
