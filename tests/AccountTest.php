<?php

namespace Nylas\Tests;

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
class AccountTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testGetAccount(): void
    {
        $data = $this->client->Accounts()->Account()->getAccount();

        \print_r($data);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------
}
