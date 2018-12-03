<?php namespace NylasTest;

/**
 * ----------------------------------------------------------------------------------
 * Account Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2018/11/28
 */
class AccountTest extends Abs
{

    // ------------------------------------------------------------------------------

    public function testGetAccount()
    {
        $data = self::$api->Accounts()->Account()->getAccount();

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

}
