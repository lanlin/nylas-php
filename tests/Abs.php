<?php namespace NylasTest;

use Nylas\Client;
use PHPUnit\Framework\TestCase;

/**
 * ----------------------------------------------------------------------------------
 * Account Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2020/04/26
 */
class Abs extends TestCase
{

    // ------------------------------------------------------------------------------

    /**
     * @var Client
     */
    protected static Client $api;

    // ------------------------------------------------------------------------------

    /**
     * init client instance
     */
    public static function setUpBeforeClass() : void
    {
        parent::setUpBeforeClass();

        $options =
        [
            'debug'         => true,
            'log_file'      => __DIR__. '/test.log',
            'account_id'    => 'your account id',
            'access_token'  => 'your access token',
            'client_id'     => 'your client id',
            'client_secret' => 'your client secret'
        ];

        self::$api = new Client($options);
    }

    // ------------------------------------------------------------------------------

}
