<?php

namespace Database\Factories;

use App\Models\UsableSite;
use App\Models\User;
use App\Models\Company;
use App\Models\Operator;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * UsableSiteFactory
 *
 * このファクトリは、使用可能なサイトデータを生成します。
 *
 * 主な仕様:
 * - エンティティタイプ、エンティティID、サイトID、共有ログイン許可フラグを生成します。
 *
 * 制限事項:
 * - 存在しないエンティティやサイトへの関連付けは行いません。
 */
class UsableSiteFactory extends Factory
{
    /**
     * モデルと対応するファクトリの定義
     *
     * @var string
     */
    protected $model = UsableSite::class;

    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // ランダムにエンティティタイプを選択
        $entityTypes = [
            User::class,
            Company::class,
            Operator::class,
        ];
        $entityType = $this->faker->randomElement($entityTypes);

        return [
            'entity_type' => $entityType,
            'entity_id' => function (array $attributes) use ($entityType) {
                return $entityType::factory()->create()->id;
            },
            'site_id' => Site::factory(),
            'shared_login_allowed' => $this->faker->boolean(60), // 60%の確率で共有ログインを許可
        ];
    }

    /**
     * 特定のエンティティタイプで使用可能なサイトを生成
     *
     * @param string $entityType
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function ofEntityType(string $entityType): static
    {
        return $this->state(fn (array $attributes) => [
            'entity_type' => $entityType,
            'entity_id' => (new $entityType)->factory()->create()->id,
        ]);
    }

    /**
     * 特定のエンティティに関連付けられた使用可能なサイトを生成
     *
     * @param \Illuminate\Database\Eloquent\Model $entity
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forEntity($entity): static
    {
        return $this->state(fn (array $attributes) => [
            'entity_type' => get_class($entity),
            'entity_id' => $entity->id,
        ]);
    }

    /**
     * 共有ログインを許可した使用可能なサイトを生成
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withSharedLogin(): static
    {
        return $this->state(fn (array $attributes) => [
            'shared_login_allowed' => true,
        ]);
    }

    /**
     * 共有ログインを許可しない使用可能なサイトを生成
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withoutSharedLogin(): static
    {
        return $this->state(fn (array $attributes) => [
            'shared_login_allowed' => false,
        ]);
    }
}
