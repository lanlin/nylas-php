<?php

namespace NylasTest;

/**
 * ----------------------------------------------------------------------------------
 * Account Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2020/04/26
 *
 * @internal
 */
class AccountTest extends Abs
{
    // ------------------------------------------------------------------------------

    public function testGetAccount(): void
    {
        $data = self::$api->Accounts()->Account()->getAccount();

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------
}
