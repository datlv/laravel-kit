<?php namespace Datlv\Kit\Testing;

use Closure;

/**
 * Class TestCase
 * @package Datlv\File\Tests\Stubs
 */
class TestCase extends \Orchestra\Testbench\TestCase
{
    public function assertHTTPExceptionStatus($expectedStatusCode, Closure $codeThatShouldThrow)
    {
        try {
            $codeThatShouldThrow($this);
            $this->assertFalse(true, "An HttpException should have been thrown by the provided Closure.");
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            // assertResponseStatus() won't work because the response object is null
            $this->assertEquals(
                $expectedStatusCode,
                $e->getStatusCode(),
                sprintf("Expected an HTTP status of %d but got %d.", $expectedStatusCode, $e->getStatusCode())
            );
        }
    }

    protected function setUp()
    {
        parent::setUp();
        $this->artisan('migrate', ['--database' => 'testing']);
    }

    /**
     * @param  \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Datlv\Kit\ServiceProvider::class,
            \Orchestra\Database\ConsoleServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * Resolve application HTTP exception handler.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function resolveApplicationExceptionHandler($app)
    {
        $app->singleton('Illuminate\Contracts\Debug\ExceptionHandler', 'Datlv\Kit\Testing\ExceptionHandler');
    }
}
