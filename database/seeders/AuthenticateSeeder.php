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
        Authenticate::create([
            'auth_code' => Str::uuid(),
            'site_id' => 1, // 必要に応じて適切な site_id に変更
            'entity_type' => 'App\Models\User', // 対象のエンティティタイプ
            'entity_id' => 1, // 必要に応じて適切なユーザーIDに変更
            'login_code' => 'test_anada', // サンプルのログインID
            'password' => Hash::make('test_anada'), // パスワードをハッシュ化して保存
            'expires_at' => now()->addMonth(), // 有効期限を設定
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Authenticate::create([
            'auth_code' => Str::uuid(),
            'site_id' => 1, // 必要に応じて適切な site_id に変更
            'entity_type' => 'App\Models\User', // 対象のエンティティタイプ
            'entity_id' => 2, // 必要に応じて適切なユーザーIDに変更
            'login_code' => 'test_user', // サンプルのログインID
            'password' => Hash::make('test_user'), // パスワードをハッシュ化して保存
            'expires_at' => now()->addMonth(), // 有効期限を設定
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }
}
