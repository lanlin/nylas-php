<?php

namespace NylasTest;

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
class ManageTest extends Abs
{
    // ------------------------------------------------------------------------------

    public function testGetAccountList(): void
    {
        $data = self::$api->Accounts()->Manage()->getAccountsList();

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testGetAccountInfo(): void
    {
        $data = self::$api->Accounts()->Manage()->getAccountInfo();

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testReactiveAccount(): void
    {
        $data = self::$api->Accounts()->Manage()->reactiveAccount();

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testGetTokenInfo(): void
    {
        $data = self::$api->Accounts()->Manage()->getTokenInfo();

        $this->assertArrayHasKey('state', $data);
    }

    // ------------------------------------------------------------------------------

    public function testGetApplication(): void
    {
        $data = self::$api->Accounts()->Manage()->getApplication();

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

        $data = self::$api->Accounts()->Manage()->updateApplication($param);

        $this->assertArrayHasKey('application_name', $data);
    }

    // ------------------------------------------------------------------------------
}
