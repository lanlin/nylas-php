<?php

namespace Tests;

use Mockery;
use Nylas\Client;
use Faker\Factory;
use Faker\Generator;
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
 * @link https://developer.nylas.com/docs/api/#overview
 * @author lanlin
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

    /**
     * @var Generator
     */
    protected Generator $faker;

    // ------------------------------------------------------------------------------

    /**
     * init client instance
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();

        $options =
        [
            'debug'         => $this->faker->randomElement([true, false]),
            'region'        => $this->faker->randomElement(['us', 'canada', 'ireland']),
            'log_file'      => __DIR__.'/test.log',
            'client_id'     => $this->faker->uuid,
            'client_secret' => $this->faker->password,
            'access_token'  => $this->faker->password,
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
