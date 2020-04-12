<?php


namespace Tests\Unit\Providers;


use Bluewing\Auth\Services\JwtManager;
use Bluewing\Auth\Services\RefreshTokenManager;
use Bluewing\Providers\JsonWebTokenServiceProvider;
use Bluewing\Providers\RefreshTokenServiceProvider;
use Illuminate\Foundation\Application;
use Mockery;
use PHPUnit\Framework\TestCase;

final class RefreshTokenServiceProviderTest extends TestCase
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var RefreshTokenServiceProvider
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
        $this->provider = new RefreshTokenServiceProvider($this->app);
        $this->assertInstanceOf(RefreshTokenServiceProvider::class, $this->provider);
    }

    /**
     *
     */
    public function test_register_binds_an_instance_of_refresh_token_manager_to_the_container(): void
    {
        $this->provider = new RefreshTokenServiceProvider($this->app);
        $this->provider->register();

        $this->assertArrayHasKey(RefreshTokenManager::class, $this->app->getBindings());
    }

}
