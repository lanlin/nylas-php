<?php

namespace Tests;

use Mockery;
use Nylas\Client;
use ReflectionMethod;
use Mockery\MockInterface;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Mockery\LegacyMockInterface;
use GuzzleHttp\Handler\MockHandler;

/**
 * ----------------------------------------------------------------------------------
 * Account Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2021/07/18
 *
 * @internal
 */
class AbsCase extends TestCase
{
    // ------------------------------------------------------------------------------

    /**
     * @var Client
     */
    protected Client $client;

    // ------------------------------------------------------------------------------

    /**
     * init client instance
     */
    public function setUp(): void
    {
        parent::setUp();

        $options =
        [
            'debug'         => true,
            'region'        => 'oregon',
            'log_file'      => __DIR__.'/test.log',
            'account_id'    => 'fgajlgadlfjlsfdl',
            'access_token'  => 'fdsalfjadlsfjlasdl',
            'client_id'     => 'falfjsdalflsdfdsf',
            'client_secret' => 'sdafadlsfaldsfjlsl',
        ];

        $this->client = new Client($options);
    }

    // ------------------------------------------------------------------------------

    /**
     * reset client
     */
    public function tearDown(): void
    {
        parent::tearDown();

        unset($this->client);

        Mockery::close();
    }

    // ------------------------------------------------------------------------------

    /**
     * assert passed
     */
    protected function assertPassed(): void
    {
        $this->assertTrue(true);
    }

    // ------------------------------------------------------------------------------

    /**
     * spy with mockery
     *
     * @param mixed ...$args
     *
     * @return LegacyMockInterface|MockInterface
     */
    protected function spy(mixed ...$args): MockInterface | LegacyMockInterface
    {
        return Mockery::spy(...$args);
    }

    // ------------------------------------------------------------------------------

    /**
     * mock with mockery
     *
     * @param mixed ...$args
     *
     * @return LegacyMockInterface|MockInterface
     */
    protected function mock(mixed ...$args): MockInterface | LegacyMockInterface
    {
        return Mockery::mock(...$args);
    }

    // ------------------------------------------------------------------------------

    /**
     * overload with mockery
     *
     * @param string $class
     *
     * @return LegacyMockInterface|MockInterface
     */
    protected function overload(string $class): MockInterface | LegacyMockInterface
    {
        return Mockery::mock('overload:'.$class);
    }

    // ------------------------------------------------------------------------------

    /**
     * call private or protected method
     *
     * @param object $object
     * @param string $method
     * @param mixed  ...$params
     *
     * @return mixed
     */
    protected function call(object $object, string $method, mixed ...$params): mixed
    {
        $method = new ReflectionMethod($object, $method);
        $method->setAccessible(true);

        return $method->invoke($object, ...$params);
    }

    // ------------------------------------------------------------------------------

    /**
     * mock any class
     *
     * @param  string  $name
     * @param  array   $mock
     *
     * @return \Mockery\MockInterface
     */
    protected function mockClass(string $name, array $mock): MockInterface
    {
        $mod = $this->overload($name)->makePartial();

        foreach ($mock as $method => $return)
        {
            $mod->shouldReceive($method)->andReturn($return);
        }

        return $mod;
    }

    // ------------------------------------------------------------------------------

    /**
     * mock api response data
     *
     * @param  array  $data
     * @param  array  $header
     * @param  int    $code
     */
    protected function mockResponse(array $data, array $header = [], int $code = 200): void
    {
        $body = \json_encode($data);

        $header = \array_merge($header, ['Content-Type' => 'application/json']);

        $mock = new MockHandler([new Response($code, $header, $body)]);

        $this->client->Options()->setHandler($mock);
    }

    // ------------------------------------------------------------------------------
}
