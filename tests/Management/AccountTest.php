<?php

namespace Tests\Management;

use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Manage Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/07/20
 *
 * @internal
 */
class AccountTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testGetAccountDetail(): void
    {
        $this->mockResponse([
            'id'                => 'awa6ltos76vz5hvphkp8k17nt',
            'object'            => 'account',
            'account_id'        => 'awa6ltos76vz5hvphkp8k17nt',
            'name'              => 'Dorothy Vaughan',
            'provider'          => 'gmail',
            'organization_unit' => 'label',
            'sync_state'        => 'running',
            'linked_at'         => 1470231381,
            'email_address'     => 'dorothy@spacetech.com',
        ]);

        $data = $this->client->Management()->Account()->getAccountDetail();

        $this->assertTrue(!empty($data['id']));
    }

    // ------------------------------------------------------------------------------

    public function testGetAccountList(): void
    {
        $this->mockResponse([[
            'account_id'    => '622x1k5v1ujh55t6ucel7av4',
            'billing_state' => 'free',
            'email'         => 'example@example.com',
            'id'            => '622x1k5v1ujh55t6ucel7av4',
            'provider'      => 'yahoo',
            'sync_state'    => 'running',
            'trial'         => false,
        ], [
            'account_id'    => '123rvgm1iccsgnjj7nn6jwu1',
            'billing_state' => 'paid',
            'email'         => 'example@example.com',
            'id'            => '123rvgm1iccsgnjj7nn6jwu1',
            'provider'      => 'gmail',
            'sync_state'    => 'running',
            'trial'         => false,
        ]]);

        $para = [
            'offset' => 0,
            'limit'  => 10,
        ];

        $data = $this->client->Management()->Account()->getAccountsList($para);

        $this->assertTrue(!empty($data[0]['id']));
    }

    // ------------------------------------------------------------------------------

    public function testGetAccountInfo(): void
    {
        $this->mockResponse([
            'account_id'    => '123rvgm1iccsgnjj7nn6jwu1',
            'billing_state' => 'paid',
            'email'         => 'example@example.com',
            'id'            => '123rvgm1iccsgnjj7nn6jwu1',
            'provider'      => 'gmail',
            'sync_state'    => 'running',
            'trial'         => false,
        ]);

        $data = $this->client->Management()->Account()->getAccountInfo('sfadfadg');

        $this->assertTrue(!empty($data['id']));
    }

    // ------------------------------------------------------------------------------

    public function testDeleteAccount(): void
    {
        $this->mockResponse([]);

        $this->client->Management()->Account()->deleteAccount('sdfadgasdg');

        $this->assertPassed();
    }

    // ------------------------------------------------------------------------------

    public function testGetTokenInfo(): void
    {
        $this->mockResponse([
            'created_at' => 1563496685,
            'scopes'     => 'calendar,email,contacts',
            'state'      => 'valid',
            'updated_at' => 1563496685,
        ]);

        $data = $this->client->Management()->Account()->getTokenInfo('sfasdgdag');

        $this->assertTrue(!empty($data['state']));
    }

    // ------------------------------------------------------------------------------

    public function testReactiveAccount(): void
    {
        $this->mockResponse(['success' => 'true']);

        $data = $this->client->Management()->Account()->reactiveAccount('safadgad');

        $this->assertTrue(!empty($data['success']));
    }

    // ------------------------------------------------------------------------------
}
