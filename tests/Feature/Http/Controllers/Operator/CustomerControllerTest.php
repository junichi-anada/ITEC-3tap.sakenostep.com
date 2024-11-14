<?php

namespace Tests\Feature\Http\Controllers\Operator;

use Tests\TestCase;
use App\Models\User;
use App\Models\Site;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // テスト用の会社とサイトを作成
        $company = Company::factory()->create();
        $site = Site::factory()->create(['company_id' => $company->id]);

        // テスト用のオペレーターを作成
        $this->user = User::factory()->create(['site_id' => $site->id]);

        // オペレーターを認証
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_register_a_customer()
    {
        $response = $this->postJson(route('operator.customer.store'), [
            'user_code' => 'U12345',
            'name' => 'Test User',
            'postal_code' => '123-4567',
            'phone' => '090-1234-5678',
            'phone2' => '080-1234-5678',
            'fax' => '03-1234-5678',
            'address' => 'Test Address',
            'login_code' => 'test_login_code',
            'password' => 'test_password',
            'site_id' => $this->user->site_id,
        ]);

        $response->assertStatus(201)
                 ->assertJson(['message' => 'success']);
    }

    // 他のテストメソッド（バリデーションエラー、認証エラーなど）もここに追加できます
}
