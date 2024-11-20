<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Site;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * モデルと対応するファクトリの定義
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_code' => $this->faker->unique()->regexify('[A-Z0-9]{6}'),
            'company_name' => $this->faker->company(),
            'name' => $this->faker->name(),
            'postal_code' => $this->faker->postcode(),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'phone2' => $this->faker->optional()->phoneNumber(),
            'fax' => $this->faker->optional()->phoneNumber(),
        ];
    }

    /**
     * 有効な公開サイトを指定された数だけ関連付けるファクトリメソッド
     *
     * @param int $count
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withPublishedSites(int $count = 3): static
    {
        return $this->has(Site::factory()->count($count), 'sites');
    }
}
