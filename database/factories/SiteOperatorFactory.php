<?php

namespace Database\Factories;

use App\Models\SiteOperator;
use App\Models\Site;
use App\Models\Operator;
use App\Models\SiteOperatorRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * SiteOperatorFactory
 *
 * このファクトリは、サイトオペレーター（サイト管理者）データを生成します。
 *
 * 主な仕様:
 * - サイトID、オペレーターID、ロールIDを生成します。
 *
 * 制限事項:
 * - 存在しないサイト、オペレーター、またはロールへの関連付けは行いません。
 */
class SiteOperatorFactory extends Factory
{
    /**
     * モデルと対応するファクトリの定義
     *
     * @var string
     */
    protected $model = SiteOperator::class;

    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'site_id' => Site::factory(), // 関連するサイト
            'operator_id' => Operator::factory(), // 関連するオペレーター
            'role_id' => SiteOperatorRole::factory(), // 関連するロール
        ];
    }

    /**
     * 特定のロールを持つサイトオペレーターを生成
     *
     * @param \App\Models\SiteOperatorRole $role
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withRole(SiteOperatorRole $role): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => $role->id,
        ]);
    }

    /**
     * 特定のサイトに関連付けられたサイトオペレーターを生成
     *
     * @param \App\Models\Site $site
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forSite(Site $site): static
    {
        return $this->state(fn (array $attributes) => [
            'site_id' => $site->id,
        ]);
    }

    /**
     * 特定のオペレーターに関連付けられたサイトオペレーターを生成
     *
     * @param \App\Models\Operator $operator
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forOperator(Operator $operator): static
    {
        return $this->state(fn (array $attributes) => [
            'operator_id' => $operator->id,
        ]);
    }
}
