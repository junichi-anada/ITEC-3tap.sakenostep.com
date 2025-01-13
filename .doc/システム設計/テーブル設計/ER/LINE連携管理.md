%% -----------------------------------
%% LINE連携管理 (Linking Management)
%% version 1.0
%% -----------------------------------
erDiagram
  Authenticates ||--o{ AuthenticateLineLinks : "linked to"
  LineAccounts ||--o{ AuthenticateLineLinks : "linked with"

  %% 15-LineAccounts
  %% LINEアカウント情報管理テーブル
  LineAccounts {
    integer id PK "AUTO_INCREMENT" %% LINEアカウントID
    string(64) line_user_id UK "NOT_NULL" %% LINEユーザーID（ユニーク）
    string(64) display_name %% LINE表示名
    string(255) access_token %% アクセストークン
    string(255) refresh_token %% リフレッシュトークン
    datetime token_expiry %% トークン有効期限
    datetime created_at "NOT_NULL" %% 作成日時
    datetime updated_at %% 更新日時
  }

  %% 16-AuthenticateLineLinks
  %% 認証とLINEアカウントのリンク情報管理
  AuthenticateLineLinks {
    integer id PK "AUTO_INCREMENT" %% リンクID
    reference authenticate_id FK "NOT_NULL: Authenticates.id" %% 認証レコードID
    reference line_account_id FK "NOT_NULL: LineAccounts.id" %% LINEアカウントID
    string(128) nonce "NOT_NULL" %% 連携用のワンタイムトークン
    string(255) link_token %% プロバイダ発行の連携トークン
    datetime linked_at %% 連携日時
    datetime unlink_at %% 連携解除日時
    datetime created_at "NOT_NULL" %% 作成日時
    datetime updated_at %% 更新日時
  }
