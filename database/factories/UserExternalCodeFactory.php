<?php

namespace Database\Factories;

use App\Models\UserExternalCode;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * UserExternalCodeFactory
 *
 * このファクトリは、ユーザーの外部コードデータを生成します。
 *
 * 主な仕様:
 * - ユーザーID、外部コードを生成します。
 *
 * 制限事項:
 * - 存在しないユーザーへの関連付けは行いません。
 */
class UserExternalCodeFactory extends Factory
{
    /**
     * モデルと対応するファクトリの定義
     *
     * @var string
     */
    protected $model = UserExternalCode::class;

    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // 関連するユーザー
            'external_code' => strtoupper($this->faker->unique()->bothify('EXT####')), // 例: EXT1234
        ];
    }

    /**
     * 特定のユーザーに関連付けられた外部コードを生成
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * 特定の外部コードを持つユーザー外部コードを生成
     *
     * @param string $externalCode
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withExternalCode(string $externalCode): static
    {
        return $this->state(fn (array $attributes) => [
            'external_code' => strtoupper($externalCode),
        ]);
    }
}
