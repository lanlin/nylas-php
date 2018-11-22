<?php namespace NylasTest;

use Nylas\Client;
use PHPUnit\Framework\TestCase;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Unit Tests Utils
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2018/11/22
 */
class Manage extends TestCase
{

    // ------------------------------------------------------------------------------

    public function testGetAccountsList()
    {
        $data =
        [
            "id"=> "awa6ltos76vz5hvphkp8k17nt",
            "account_id"=> "awa6ltos76vz5hvphkp8k17nt",
            "sync_state"=> "running",
            "billing_state"=> "running",
        ];
    }

    // ------------------------------------------------------------------------------

}
