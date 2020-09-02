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
 * @change 2020/06/27
 *
 * @internal
 */
class AbsCase extends TestCase
{
    // ------------------------------------------------------------------------------

    /**
     * @var Client
     */
    protected $client;

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

        $ENVS = \getenv('TESTING_ENVS');

        if (!empty($ENVS))
        {
            $options = \base64_decode($ENVS, true);
            $options = \json_decode($options, true, 512);
        }

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
