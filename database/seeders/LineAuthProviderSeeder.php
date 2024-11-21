<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LineAuthProviderSeeder extends Seeder
{
    /**
     * LINE認証プロバイダーのシーディングを実行
     */
    public function run(): void
    {
        DB::table('auth_providers')->updateOrInsert(
            [
                'provider_code' => 'line',
            ],
            [
                'name' => 'LINE',
                'description' => 'LINE Messaging APIによる認証',
                'is_enable' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('site_auth_providers')->updateOrInsert(
            [
                'site_id' => config('services.line.site_id'),
                'auth_provider_id' => DB::table('auth_providers')
                    ->where('provider_code', 'line')
                    ->value('id'),
            ],
            [
                'is_enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
