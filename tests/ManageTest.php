<?php namespace NylasTest;

/**
 * ----------------------------------------------------------------------------------
 * Manage Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2018/11/28
 */
class ManageTest extends Abs
{

    // ------------------------------------------------------------------------------

    public function testGetAccountList()
    {
        $data = self::$api->Accounts()->Manage()->getAccountsList();

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testGetAccountInfo()
    {
        $data = self::$api->Accounts()->Manage()->getAccountInfo();

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testReactiveAccount()
    {
        $data = self::$api->Accounts()->Manage()->reactiveAccount();

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

}
