<?php

namespace Tests\Feature\Services\Operator\Customer\Delete;

use App\Models\User;
use App\Models\Authenticate;
use App\Models\Site;
use App\Models\Company;
use App\Services\Operator\Customer\Delete\CustomerDeleteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class CustomerDeleteServiceTest extends TestCase
{
    use RefreshDatabase;

    private $customerDeleteService;
    private $site;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a company and a site for testing
        $company = Company::factory()->create();
        $this->site = Site::factory()->create(['company_id' => $company->id]);

        $this->customerDeleteService = $this->app->make(CustomerDeleteService::class);
    }

    public function testCustomerDeletionSuccess()
    {
        // Create a user and authenticate record
        $user = User::factory()->create(['site_id' => $this->site->id, 'user_code' => 'U12345']);
        Authenticate::factory()->create(['site_id' => $this->site->id, 'entity_id' => $user->id]);

        // Simulate the operator being logged in
        $operator = User::factory()->create(['site_id' => $this->site->id]);
        $this->actingAs($operator);

        $request = new Request([
            'site_id' => $this->site->id,
            'user_code' => 'U12345',
        ]);

        $response = $this->customerDeleteService->deleteCustomer($request);
        $result = $response->getData(true);

        $this->assertEquals('success', $result['message']);
        $this->assertSoftDeleted('users', ['user_code' => 'U12345']);
        $this->assertSoftDeleted('authenticates', ['entity_id' => $user->id]);
    }

    public function testCustomerDeletionFailure()
    {
        // テストデータ: 存在しないuser_codeを指定
        $request = new Request([
            'site_id' => $this->site->id,
            'user_code' => 'U99999', // Non-existent user_code
        ]);

        // Simulate the operator being logged in
        $operator = User::factory()->create(['site_id' => $this->site->id]);
        $this->actingAs($operator);

        $response = $this->customerDeleteService->deleteCustomer($request);
        $result = $response->getData(true);

        $this->assertEquals('fail', $result['message']);
        $this->assertStringContainsString('not exist', $result['reason']);
    }

    public function testCustomerDeletionValidationErrors()
    {
        $request = new Request([
            // 'site_id' is missing
            'user_code' => 'U12345',
        ]);

        // Simulate the operator being logged in
        $operator = User::factory()->create(['site_id' => $this->site->id]);
        $this->actingAs($operator);

        $response = $this->customerDeleteService->deleteCustomer($request);
        $result = $response->getData(true);

        $this->assertEquals('fail', $result['message']);
        $this->assertStringContainsString('The site_id field is required.', $result['reason']);
    }

    public function testCustomerDeletionUnauthorized()
    {
        // Ensure no user is authenticated
        Auth::logout();

        $request = new Request([
            'site_id' => $this->site->id,
            'user_code' => 'U12345',
        ]);

        $response = $this->customerDeleteService->deleteCustomer($request);
        $result = $response->getData(true);

        $this->assertEquals('fail', $result['message']);
        $this->assertStringContainsString('Unauthorized access', $result['reason']);
    }
}
