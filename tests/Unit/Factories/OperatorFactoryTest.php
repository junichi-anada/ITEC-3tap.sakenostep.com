<?php

namespace Tests\Unit\Factories;

use Tests\TestCase;
use App\Models\Operator;
use App\Models\Company;
use App\Models\OperatorRank;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * OperatorFactoryのテストクラス
 */
final class OperatorFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * OperatorFactoryが正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_operator()
    {
        $operator = Operator::factory()->create();

        // データベースにレコードが存在することを確認
        $this->assertDatabaseHas('operators', [
            'id' => $operator->id,
            'operator_code' => $operator->operator_code,
            'company_id' => $operator->company_id,
            'name' => $operator->name,
            'operator_rank_id' => $operator->operator_rank_id,
        ]);

        // 属性が期待通りであることを確認
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{6}$/', $operator->operator_code);
        $this->assertInstanceOf(Company::class, $operator->company);
        $this->assertInstanceOf(OperatorRank::class, $operator->operatorRank);
        $this->assertNotEmpty($operator->name);
    }

    /**
     * OperatorFactoryが一意なoperator_codeを生成することをテストします。
     *
     * @return void
     */
    public function test_it_generates_unique_operator_code()
    {
        $operator1 = Operator::factory()->create();
        $operator2 = Operator::factory()->create();

        $this->assertNotEquals($operator1->operator_code, $operator2->operator_code);
    }

    /**
     * OperatorFactoryが特定の会社に関連付けられたオペレーターを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_operator_for_specific_company()
    {
        $company = Company::factory()->create();
        $operator = Operator::factory()->forCompany($company)->create();

        $this->assertEquals($company->id, $operator->company_id);
        $this->assertDatabaseHas('operators', [
            'id' => $operator->id,
            'company_id' => $company->id,
        ]);
    }

    /**
     * OperatorFactoryが特定のランクに関連付けられたオペレーターを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_operator_with_specific_rank()
    {
        $rank = OperatorRank::factory()->create(['name' => 'マネージャー']);
        $operator = Operator::factory()->withRank($rank)->create();

        $this->assertEquals($rank->id, $operator->operator_rank_id);
        $this->assertDatabaseHas('operators', [
            'id' => $operator->id,
            'operator_rank_id' => $rank->id,
        ]);
    }

    /**
     * OperatorFactoryが関連する会社とランクを正しく作成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_related_company_and_rank()
    {
        $company = Company::factory()->create();
        $rank = OperatorRank::factory()->create(['name' => 'スーパーバイザー']);

        $operator = Operator::factory()->create([
            'company_id' => $company->id,
            'operator_rank_id' => $rank->id,
        ]);

        $this->assertEquals($company->id, $operator->company_id);
        $this->assertEquals($rank->id, $operator->operator_rank_id);
        $this->assertInstanceOf(Company::class, $operator->company);
        $this->assertInstanceOf(OperatorRank::class, $operator->operatorRank);
        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
        ]);
        $this->assertDatabaseHas('operator_ranks', [
            'id' => $rank->id,
        ]);
    }

    /**
     * OperatorFactoryが特定の名前を持つオペレーターを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_operator_with_specific_name()
    {
        $specificName = '田中 一郎';
        $operator = Operator::factory()->withName($specificName)->create();

        $this->assertEquals($specificName, $operator->name);
        $this->assertDatabaseHas('operators', [
            'id' => $operator->id,
            'name' => $specificName,
        ]);
    }
}
