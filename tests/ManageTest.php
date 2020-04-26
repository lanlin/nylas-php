<?php namespace NylasTest;

/**
 * ----------------------------------------------------------------------------------
 * Manage Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2020/04/26
 */
class ManageTest extends Abs
{

    // ------------------------------------------------------------------------------

    public function testGetAccountList() : void
    {
        $data = self::$api->Accounts()->Manage()->getAccountsList();

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testGetAccountInfo() : void
    {
        $data = self::$api->Accounts()->Manage()->getAccountInfo();

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testReactiveAccount() : void
    {
        $data = self::$api->Accounts()->Manage()->reactiveAccount();

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

}
