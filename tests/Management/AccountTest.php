<?php

namespace Tests\Management;

use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Manage Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/22
 *
 * @internal
 */
class AccountTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testReturnAccountDetails(): void
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

        $data = $this->client->Management->Account->returnAccountDetails();

        $this->assertTrue(!empty($data['id']));
    }

    // ------------------------------------------------------------------------------

    public function testReturnAllAccounts(): void
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

        $data = $this->client->Management->Account->returnAllAccounts($para);

        $this->assertTrue(!empty($data[0]['id']));
    }

    // ------------------------------------------------------------------------------

    public function testReturnAnAccount(): void
    {
        $id = $this->faker->uuid;

        $this->mockResponse([
            'account_id'    => '123rvgm1iccsgnjj7nn6jwu1',
            'billing_state' => 'paid',
            'email'         => 'example@example.com',
            'id'            => '123rvgm1iccsgnjj7nn6jwu1',
            'provider'      => 'gmail',
            'sync_state'    => 'running',
            'trial'         => false,
        ]);

        $data = $this->client->Management->Account->returnAnAccount($id);

        $this->assertTrue(!empty($data['id']));
    }

    // ------------------------------------------------------------------------------

    public function testDeleteAnAccount(): void
    {
        $this->mockResponse([]);

        $id = $this->faker->uuid;

        $this->client->Management->Account->deleteAnAccount($id);

        $this->assertPassed();
    }

    // ------------------------------------------------------------------------------

    public function testReactiveAnAccount(): void
    {
        $this->mockResponse(['success' => 'true']);

        $id = $this->faker->uuid;

        $data = $this->client->Management->Account->reactiveAnAccount($id);

        $this->assertTrue(!empty($data['success']));
    }

    // ------------------------------------------------------------------------------

    public function testReturnTokenInformation(): void
    {
        $this->mockResponse([
            'created_at' => 1563496685,
            'scopes'     => 'calendar,email,contacts',
            'state'      => 'valid',
            'updated_at' => 1563496685,
        ]);

        $id = $this->faker->uuid;

        $data = $this->client->Management->Account->returnTokenInformation($id);

        $this->assertTrue(!empty($data['state']));
    }

    // ------------------------------------------------------------------------------
}
