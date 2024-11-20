<?php

namespace Database\Factories;

use App\Models\Operator;
use App\Models\Company;
use App\Models\OperatorRank;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * OperatorFactory
 *
 * このファクトリは、オペレーター（管理者）データを生成します。
 *
 * 主な仕様:
 * - オペレーターコード、会社ID、名前、ランクIDを生成します。
 *
 * 制限事項:
 * - 存在しない会社やランクへの関連付けは行いません。
 */
class OperatorFactory extends Factory
{
    /**
     * モデルと対応するファクトリの定義
     *
     * @var string
     */
    protected $model = Operator::class;

    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'operator_code' => $this->faker->unique()->regexify('[A-Z0-9]{6}'), // 6文字のユニークな英数字コード
            'company_id' => Company::factory(), // 関連する会社
            'name' => $this->faker->name(), // オペレーターの名前
            'operator_rank_id' => OperatorRank::factory(), // 関連するランク
        ];
    }

    /**
     * 特定のランクを持つオペレーターを生成
     *
     * @param \App\Models\OperatorRank $rank
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withRank(OperatorRank $rank): static
    {
        return $this->state(fn (array $attributes) => [
            'operator_rank_id' => $rank->id,
        ]);
    }

    /**
     * 特定の会社に所属するオペレーターを生成
     *
     * @param \App\Models\Company $company
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forCompany(Company $company): static
    {
        return $this->state(fn (array $attributes) => [
            'company_id' => $company->id,
        ]);
    }
}
