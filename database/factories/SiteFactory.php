<?php

namespace Database\Factories;

use App\Models\Site;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * SiteFactory
 *
 * このファクトリは、サイトデータを生成します。
 *
 * 主な仕様:
 * - サイトコード、会社ID、URL、名前、説明、B2Bフラグを生成します。
 *
 * 制限事項:
 * - 存在しない会社への関連付けは行いません。
 */
class SiteFactory extends Factory
{
    /**
     * モデルと対応するファクトリの定義
     *
     * @var string
     */
    protected $model = Site::class;

    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'site_code' => $this->faker->unique()->regexify('[A-Z0-9]{6}'), // 6文字のユニークな英数字コード
            'company_id' => Company::factory(), // 関連する会社
            'url' => $this->faker->unique()->url(), // サイトのURL
            'name' => $this->faker->company(), // サイトの名前
            'description' => $this->faker->sentence(), // サイトの説明
            'is_btob' => $this->faker->boolean(70), // 70%の確率でB2Bサイト
        ];
    }

    /**
     * B2Bサイトを生成
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function b2b(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_btob' => true,
        ]);
    }

    /**
     * B2Cサイトを生成
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function b2c(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_btob' => false,
        ]);
    }
}
