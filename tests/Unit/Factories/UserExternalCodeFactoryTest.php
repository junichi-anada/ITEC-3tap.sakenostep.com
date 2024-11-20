<?php

namespace Tests\Unit\Factories;

use Tests\TestCase;
use App\Models\UserExternalCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * UserExternalCodeFactoryのテストクラス
 */
final class UserExternalCodeFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * UserExternalCodeFactoryが正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_user_external_code()
    {
        $userExternalCode = UserExternalCode::factory()->create();

        // データベースにレコードが存在することを確認
        $this->assertDatabaseHas('user_external_codes', [
            'id' => $userExternalCode->id,
            'user_id' => $userExternalCode->user_id,
            'external_code' => $userExternalCode->external_code,
        ]);

        // 属性が期待通りであることを確認
        $this->assertInstanceOf(User::class, $userExternalCode->user);
        $this->assertMatchesRegularExpression('/^EXT\d{4}$/', $userExternalCode->external_code);
    }

    /**
     * UserExternalCodeFactoryが一意なexternal_codeを生成することをテストします。
     *
     * @return void
     */
    public function test_it_generates_unique_external_code()
    {
        $userExternalCode1 = UserExternalCode::factory()->create();
        $userExternalCode2 = UserExternalCode::factory()->create();

        $this->assertNotEquals($userExternalCode1->external_code, $userExternalCode2->external_code);
    }

    /**
     * UserExternalCodeFactoryが特定のユーザーに関連付けられた外部コードを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_user_external_code_for_specific_user()
    {
        $user = User::factory()->create();
        $userExternalCode = UserExternalCode::factory()->forUser($user)->create();

        $this->assertEquals($user->id, $userExternalCode->user_id);
        $this->assertDatabaseHas('user_external_codes', [
            'id' => $userExternalCode->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * UserExternalCodeFactoryが特定のexternal_codeを持つ外部コードを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_user_external_code_with_specific_external_code()
    {
        $specificExternalCode = 'EXT1234';
        $userExternalCode = UserExternalCode::factory()->withExternalCode($specificExternalCode)->create();

        $this->assertEquals($specificExternalCode, $userExternalCode->external_code);
        $this->assertDatabaseHas('user_external_codes', [
            'id' => $userExternalCode->id,
            'external_code' => $specificExternalCode,
        ]);
    }

    /**
     * UserExternalCodeFactoryが複数の状態を組み合わせて正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_user_external_code_with_multiple_states()
    {
        $user = User::factory()->create();
        $specificExternalCode = 'EXT5678';

        $userExternalCode = UserExternalCode::factory()
            ->forUser($user)
            ->withExternalCode($specificExternalCode)
            ->create();

        $this->assertEquals($user->id, $userExternalCode->user_id);
        $this->assertEquals($specificExternalCode, $userExternalCode->external_code);
        $this->assertDatabaseHas('user_external_codes', [
            'id' => $userExternalCode->id,
            'user_id' => $user->id,
            'external_code' => $specificExternalCode,
        ]);
    }

    /**
     * UserExternalCodeFactoryが関連するユーザーを正しく作成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_related_user()
    {
        $userExternalCode = UserExternalCode::factory()->create();

        // 関連するユーザーが存在することを確認
        $this->assertNotNull($userExternalCode->user);
        $this->assertInstanceOf(User::class, $userExternalCode->user);
        $this->assertDatabaseHas('users', [
            'id' => $userExternalCode->user_id,
        ]);
    }
}
