<?php

namespace Tests\Accounts;

use Tests\AbsCase;

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
        $this->mockResponse([

        ]);

        $data = $this->client->Accounts()->Account()->getAccount();

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------
}
