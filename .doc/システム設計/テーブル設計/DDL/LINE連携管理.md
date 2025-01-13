-- -----------------------------------
-- LINE連携管理 (LINE Linking Management)
-- version 1.0
-- -----------------------------------

-- LineAccounts
CREATE TABLE line_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'LINEアカウントID',
    line_user_id VARCHAR(64) NOT NULL UNIQUE COMMENT 'LINEユーザーID（ユニーク）',
    display_name VARCHAR(64) DEFAULT NULL COMMENT 'LINE表示名',
    access_token VARCHAR(255) DEFAULT NULL COMMENT 'アクセストークン',
    refresh_token VARCHAR(255) DEFAULT NULL COMMENT 'リフレッシュトークン',
    token_expiry DATETIME DEFAULT NULL COMMENT 'トークン有効期限',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時'
) COMMENT='LINEアカウント情報を管理するテーブル';

-- AuthenticateLineLinks
CREATE TABLE authenticate_line_links (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'リンクID',
    authenticate_id INT NOT NULL COMMENT '認証レコードID',
    line_account_id INT NOT NULL COMMENT 'LINEアカウントID',
    nonce VARCHAR(128) NOT NULL COMMENT '連携用のワンタイムトークン',
    link_token VARCHAR(255) DEFAULT NULL COMMENT 'プロバイダ発行の連携トークン',
    linked_at DATETIME DEFAULT NULL COMMENT '連携日時',
    unlink_at DATETIME DEFAULT NULL COMMENT '連携解除日時',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
    FOREIGN KEY (authenticate_id) REFERENCES authenticates(id) ON DELETE CASCADE,
    FOREIGN KEY (line_account_id) REFERENCES line_accounts(id) ON DELETE CASCADE
) COMMENT='認証とLINEアカウントのリンク情報を管理するテーブル'; 
