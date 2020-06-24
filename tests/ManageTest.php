<?php

namespace Nylas\Tests;

/**
 * ----------------------------------------------------------------------------------
 * Manage Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2020/04/26
 *
 * @internal
 */
class ManageTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testGetAccountList(): void
    {
        $data = $this->client->Accounts()->Manage()->getAccountsList();

        $this->assertIsArray($data);
    }

    // ------------------------------------------------------------------------------

    public function testGetAccountInfo(): void
    {
        $data = $this->client->Accounts()->Manage()->getAccountInfo();

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testReactiveAccount(): void
    {
        $data = $this->client->Accounts()->Manage()->reactiveAccount();

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testGetTokenInfo(): void
    {
        $data = $this->client->Accounts()->Manage()->getTokenInfo();

        $this->assertArrayHasKey('state', $data);
    }

    // ------------------------------------------------------------------------------

    public function testGetApplication(): void
    {
        $data = $this->client->Accounts()->Manage()->getApplication();

        $this->assertArrayHasKey('application_name', $data);
    }

    // ------------------------------------------------------------------------------

    public function testUpdateApplication(): void
    {
        $param =
        [
            'application_name' => 'test_'.\time(),
            'redirect_uris'    => ['http://www.test-nylas-test.com'],
        ];

        $data = $this->client->Accounts()->Manage()->updateApplication($param);

        $this->assertArrayHasKey('application_name', $data);
    }

    // ------------------------------------------------------------------------------
}
