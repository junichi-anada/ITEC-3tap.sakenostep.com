<?php

namespace Tests\Unit\Factories;

use Tests\TestCase;
use App\Models\OperatorRank;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * OperatorRankFactoryのテストクラス
 */
final class OperatorRankFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * OperatorRankFactoryが正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_operator_rank()
    {
        $operatorRank = OperatorRank::factory()->create();

        // データベースにレコードが存在することを確認
        $this->assertDatabaseHas('operator_ranks', [
            'id' => $operatorRank->id,
            'name' => $operatorRank->name,
            'priority' => $operatorRank->priority,
        ]);

        // 属性が期待通りであることを確認
        $this->assertIsString($operatorRank->name);
        $this->assertNotEmpty($operatorRank->name);
        $this->assertIsInt($operatorRank->priority);
    }

    /**
     * OperatorRankFactoryが一意なnameを生成することをテストします。
     *
     * @return void
     */
    public function test_it_generates_unique_role_name()
    {
        $role1 = OperatorRank::factory()->create(['name' => 'マネージャー']);
        $role2 = OperatorRank::factory()->create(['name' => 'スーパーバイザー']);
        $role3 = OperatorRank::factory()->create(['name' => '閲覧者']);

        $this->assertNotEquals($role1->name, $role2->name);
        $this->assertNotEquals($role1->name, $role3->name);
        $this->assertNotEquals($role2->name, $role3->name);
    }

    /**
     * OperatorRankFactoryがwithNameメソッドを正しく適用することをテストします。
     *
     * @return void
     */
    public function test_it_creates_role_with_specific_name()
    {
        $specificName = 'テストマネージャー';
        $role = OperatorRank::factory()->withName($specificName)->create();

        $this->assertEquals($specificName, $role->name);
        $this->assertDatabaseHas('operator_ranks', [
            'id' => $role->id,
            'name' => $specificName,
        ]);
    }

    /**
     * OperatorRankFactoryがwithDescriptionメソッドを正しく適用することをテストします。
     *
     * @return void
     */
    public function test_it_creates_role_with_specific_description()
    {
        $specificDescription = 'システム全体の管理を行います。';
        $role = OperatorRank::factory()->withDescription($specificDescription)->create();

        $this->assertEquals($specificDescription, $role->description);
        $this->assertDatabaseHas('operator_ranks', [
            'id' => $role->id,
            'description' => $specificDescription,
        ]);
    }

    /**
     * OperatorRankFactoryが複数の状態を組み合わせて正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_role_with_multiple_states()
    {
        $specificName = 'テストスーパーバイザー';
        $specificDescription = 'テスト環境の管理を行います。';

        $role = OperatorRank::factory()
            ->withName($specificName)
            ->withDescription($specificDescription)
            ->create();

        $this->assertEquals($specificName, $role->name);
        $this->assertEquals($specificDescription, $role->description);
        $this->assertDatabaseHas('operator_ranks', [
            'id' => $role->id,
            'name' => $specificName,
            'description' => $specificDescription,
        ]);
    }

    /**
     * OperatorRankFactoryが関連するモデルを正しく作成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_related_models()
    {
        $role = OperatorRank::factory()->create();

        // 追加の関連モデルが存在する場合はここにテストを追加
        // 例: もしOperatorRankが他のモデルに関連している場合
    }
}
