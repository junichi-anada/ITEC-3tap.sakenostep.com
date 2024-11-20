<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * UserFactory
 *
 * このファクトリは、ユーザーデータを生成します。
 * 主な仕様:
 * - ユーザーコード、サイトID、名前、郵便番号、住所、電話番号、FAX番号を生成します。
 * - パスワードはデフォルトでハッシュ化された「password」を使用します。
 * 制限事項:
 * - 存在しないサイトへの関連付けは行いません。
 */
class UserFactory extends Factory
{
    /**
     * モデルと対応するファクトリの定義
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_code' => strtoupper(Str::random(8)),
            'site_id' => Site::factory(),
            'name' => $this->faker->name,
            'postal_code' => $this->faker->postcode,
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'phone2' => $this->faker->optional()->phoneNumber,
            'fax' => $this->faker->optional()->phoneNumber,
        ];
    }

    /**
     * 特定のサイトに所属するユーザーを生成
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
     * 管理者ユーザーを生成
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_code' => 'ADMIN' . $this->faker->unique()->numberBetween(1000, 9999),
            'name' => '管理者',
        ]);
    }

    /**
     * 特定の名称を持つユーザーを生成
     *
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
        ]);
    }
}
