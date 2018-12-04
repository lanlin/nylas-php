<?php namespace NylasTest;

use Nylas\Client;
use PHPUnit\Framework\TestCase;

/**
 * ----------------------------------------------------------------------------------
 * Account Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2018/11/28
 */
class Abs extends TestCase
{

    // ------------------------------------------------------------------------------

    /**
     * @var Client
     */
    protected static $api;

    // ------------------------------------------------------------------------------

    /**
     * init client instance
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $options =
        [
            'debug'         => true,
            'log_file'      => dirname(__FILE__) . '/test.log',
            'account_id'    => 'your account id',
            'access_token'  => 'your access token',
            'client_id'     => 'your client id',
            'client_secret' => 'your client secret'
        ];

        self::$api = new Client($options);

        self::$api
        ->Contacts()
        ->Contact()
            ->
























    }

    // ------------------------------------------------------------------------------

}
