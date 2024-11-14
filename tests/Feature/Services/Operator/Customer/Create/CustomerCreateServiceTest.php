<?php

namespace Tests\Feature\Services\Operator\Customer\Create;

use App\Models\User;
use App\Models\Authenticate;
use App\Models\Site;
use App\Models\Company;
use App\Services\Operator\Customer\Create\CustomerCreateService;
use App\Services\Operator\Customer\Create\UserCreationService;
use App\Services\Operator\Customer\Create\AuthenticationCreationService;
use App\Services\Operator\Customer\Create\PhoneNumberFormatter;
use App\Services\Operator\Customer\Log\CustomerLogService;
use App\Services\Operator\Customer\Transaction\CustomerTransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;
use Mockery;

class CustomerCreateServiceTest extends TestCase
{
    use RefreshDatabase;

    private $customerCreateService;
    private $site;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a company and a site for testing
        $company = Company::factory()->create();
        $this->site = Site::factory()->create(['company_id' => $company->id]);

        $userCreationService = $this->app->make(UserCreationService::class);
        $authenticationCreationService = $this->app->make(AuthenticationCreationService::class);
        $phoneNumberFormatter = $this->app->make(PhoneNumberFormatter::class);
        $logService = $this->app->make(CustomerLogService::class);
        $transactionService = $this->app->make(CustomerTransactionService::class);

        $this->customerCreateService = new CustomerCreateService(
            $userCreationService,
            $authenticationCreationService,
            $phoneNumberFormatter,
            $logService,
            $transactionService
        );
    }

    public function testCustomerRegistrationSuccess()
    {
        // Ensure the company and site exist
        $company = Company::factory()->create();
        $site = Site::factory()->create(['company_id' => $company->id]);

        $request = new Request([
            'user_code' => 'U12345',
            'name' => 'Test User',
            'postal_code' => '1234567',
            'phone' => '090-1234-5678',
            'phone2' => '080-1234-5678',
            'fax' => '03-1234-5678',
            'address' => 'Test Address',
            'login_code' => 'test_login_code',
            'password' => 'test_password',
        ]);

        $auth = (object) ['site_id' => $site->id]; // 作成したサイトのIDを使用

        $response = $this->customerCreateService->registCustomer($request, $auth);
        $result = $response->getData(true); // JSONレスポンスを配列として取得

        $this->assertEquals('success', $result['message']);
        $this->assertDatabaseHas('users', ['user_code' => 'U12345']);
        $this->assertDatabaseHas('authenticates', [
            'login_code' => $result['login_code'],
            'site_id' => $auth->site_id,
            'entity_id' => User::where('user_code', 'U12345')->first()->id,
        ]);
    }

    public function testCustomerRegistrationFailure()
    {
        $request = new Request([
            'user_code' => 'U12345',
            'name' => 'Test User',
            'postal_code' => '1234567',
            'phone' => '090-1234-5678',
            'phone2' => '080-1234-5678',
            'fax' => '03-1234-5678',
            'address' => 'Test Address',
            'login_code' => 'test_login_code',
            'password' => 'test_password',
        ]);

        $auth = (object) ['site_id' => $this->site->id]; // Use the site created in setUp

        // Simulate a failure in user creation using a partial mock
        $userCreationServiceMock = Mockery::mock(UserCreationService::class, [
            $this->app->make(CustomerLogService::class),
            $this->app->make(CustomerTransactionService::class)
        ])->makePartial();

        $userCreationServiceMock->shouldReceive('createUser')->andThrow(new \Exception('User creation failed'));

        // Ensure the mock is correctly injected
        $this->app->instance(UserCreationService::class, $userCreationServiceMock);

        // Re-instantiate the service to ensure it uses the mocked UserCreationService
        $this->customerCreateService = new CustomerCreateService(
            $userCreationServiceMock,
            $this->app->make(AuthenticationCreationService::class),
            $this->app->make(PhoneNumberFormatter::class),
            $this->app->make(CustomerLogService::class),
            $this->app->make(CustomerTransactionService::class)
        );

        $response = $this->customerCreateService->registCustomer($request, $auth);
        $result = $response->getData(true); // JSONレスポンスを配列として取得

        $this->assertEquals('fail', $result['message']);
        $this->assertEquals('User creation failed', $result['reason']);
    }

    public function testCustomerRegistrationWithMissingFields()
    {
        $request = new Request([
            // 'user_code' is missing
            'name' => 'Test User',
            'postal_code' => '1234567',
            'phone' => '090-1234-5678',
            'phone2' => '080-1234-5678',
            'fax' => '03-1234-5678',
            'address' => 'Test Address',
            'login_code' => 'test_login_code',
            'password' => 'test_password',
        ]);

        $auth = (object) ['site_id' => $this->site->id]; // Use the site created in setUp

        $response = $this->customerCreateService->registCustomer($request, $auth);
        $result = $response->getData(true); // JSONレスポンスを配列として取得

        $this->assertEquals('fail', $result['message']);
        $this->assertStringContainsString('user_code', $result['reason']);
    }

    public function testCustomerRegistrationWithInvalidPhoneNumber()
    {
        $request = new Request([
            'user_code' => 'U12345',
            'name' => 'Test User',
            'postal_code' => '1234567',
            'phone' => 'invalid-phone', // Invalid phone number
            'phone2' => '080-1234-5678',
            'fax' => '03-1234-5678',
            'address' => 'Test Address',
            'login_code' => 'test_login_code',
            'password' => 'test_password',
        ]);

        $auth = (object) ['site_id' => $this->site->id]; // Use the site created in setUp

        $response = $this->customerCreateService->registCustomer($request, $auth);
        $result = $response->getData(true); // JSONレスポンスを配列として取得

        $this->assertEquals('fail', $result['message']);
        $this->assertStringContainsString('phone', $result['reason']);
    }
}
