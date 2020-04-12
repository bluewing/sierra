<?php


namespace Tests\Unit\Providers;


use ArrayAccess;
use Bluewing\Auth\Services\JwtManager;
use Bluewing\Providers\JsonWebTokenServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;
use Mockery;
use PHPUnit\Framework\TestCase;

final class JsonWebTokenServiceProviderTest extends TestCase
{

    /**
     * @var Application
     */
    private $app;

    /**
     * @var
     */
    private $config;

    /**
     * @var JsonWebTokenServiceProvider
     */
    private $provider;


    protected function setUp(): void
    {
        parent::setUp();

        $this->config = Mockery::mock();
        $this->app = new Application();

    }

    /**
     *
     */
    public function test_service_provider_can_be_constructed(): void
    {
        $this->provider = new JsonWebTokenServiceProvider($this->app);
        $this->assertInstanceOf(JsonWebTokenServiceProvider::class, $this->provider);
    }

    /**
     *
     */
    public function test_register_binds_an_instance_of_jwt_manager_to_the_container(): void
    {
        $this->provider = new JsonWebTokenServiceProvider($this->app);
        $this->provider->register();

        $this->assertArrayHasKey(JwtManager::class, $this->app->getBindings());
    }

    /**
     *
     */
    public function test_boot_extends_auth_with_jwt_driver_returning_jwt_guard(): void
    {
        $this->markTestIncomplete();
    }
}
