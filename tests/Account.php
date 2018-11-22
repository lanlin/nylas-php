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
class Account extends TestCase
{

    // ------------------------------------------------------------------------------

    public function testGetAccountInfo()
    {
        $data =
        [
            "id"=> "awa6ltos76vz5hvphkp8k17nt",
            "account_id"=> "awa6ltos76vz5hvphkp8k17nt",
            "object"=> "account",
            "name"=> "Ben Bitdiddle",
            "email_address"=> "benbitdiddle@gmail.com",
            "provider"=> "gmail",
            "organization_unit"=> "label",
            "sync_state"=> "running",
            "linked_at"=> 1470231381,
        ];
    }

    // ------------------------------------------------------------------------------

}
