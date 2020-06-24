<?php

namespace Nylas\Tests;

use Nylas\Client;
use PHPUnit\Framework\TestCase;

/**
 * ----------------------------------------------------------------------------------
 * Account Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2020/06/24
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
            'log_file'      => __DIR__.'/test.log',
            'account_id'    => 'your account id',
            'access_token'  => 'your access token',
            'client_id'     => 'your client id',
            'client_secret' => 'your client secret',
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
    }

    // ------------------------------------------------------------------------------
}
