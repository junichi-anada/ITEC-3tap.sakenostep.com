<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Authenticate;

class AuthenticateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 実データを挿入
        // 利用者（３アカウント）
        Authenticate::create([
            'auth_code' => "A990101ABCDE",
            'site_id' => 1, // 必要に応じて適切な site_id に変更
            'entity_type' => 'App\Models\User', // 対象のエンティティタイプ
            'entity_id' => 1, // 必要に応じて適切なユーザーIDに変更
            'login_code' => 'anada', // サンプルのログインID
            'password' => Hash::make('08010629982'), // パスワードをハッシュ化して保存
            'expires_at' => now()->addMonth(), // 有効期限を設定
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Authenticate::create([
            'auth_code' => "A990101FGHIJ",
            'site_id' => 1, // 必要に応じて適切な site_id に変更
            'entity_type' => 'App\Models\User', // 対象のエンティティタイプ
            'entity_id' => 2, // 必要に応じて適切なユーザーIDに変更
            'login_code' => 'wada', // サンプルのログインID
            'password' => Hash::make('09028461629'), // パスワードをハッシュ化して保存
            'expires_at' => now()->addMonth(), // 有効期限を設定
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Authenticate::create([
            'auth_code' => "A990101KLMNO",
            'site_id' => 1, // 必要に応じて適切な site_id に変更
            'entity_type' => 'App\Models\User', // 対象のエンティティタイプ
            'entity_id' => 3, // 必要に応じて適切なユーザーIDに変更
            'login_code' => 'numata', // サンプルのログインID
            'password' => Hash::make('233541'), // パスワードをハッシュ化して保存
            'expires_at' => now()->addMonth(), // 有効期限を設定
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // サイト管理者（１アカウント）
        Authenticate::create([
            'auth_code' => "A990101PQRST",
            'site_id' => 1, // 必要に応じて適切な site_id に変更
            'entity_type' => 'App\Models\Operator', // 対象のエンティティタイプ
            'entity_id' => 1, // 必要に応じて適切なユーザーIDに変更
            'login_code' => 'admin', // サンプルのログインID
            'password' => Hash::make('3tap2024'), // パスワードをハッシュ化して保存
            'expires_at' => now()->addMonth(), // 有効期限を設定
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }
}
