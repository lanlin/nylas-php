<?php

declare(strict_types = 1);

namespace Tests\Management;

use JsonException;
use Tests\AbsCase;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Manage Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 *
 * @internal
 */
class AccountTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
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

        static::assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testReturnAllAccounts(): void
    {
        $params = [
            'limit'  => 100,
            'offset' => 0,
        ];

        $this->mockResponse([[
            'account_id'          => '622x1k5v1ujh55t6ucel7av4',
            'billing_state'       => 'free',
            'email'               => 'example@example.com',
            'id'                  => '622x1k5v1ujh55t6ucel7av4',
            'provider'            => 'yahoo',
            'sync_state'          => 'running',
            'trial'               => false,
            'authentication_type' => '',
        ], [
            'account_id'          => '123rvgm1iccsgnjj7nn6jwu1',
            'billing_state'       => 'paid',
            'email'               => 'example@example.com',
            'id'                  => '123rvgm1iccsgnjj7nn6jwu1',
            'provider'            => 'gmail',
            'sync_state'          => 'running',
            'trial'               => false,
            'authentication_type' => '',
        ]]);

        $data = $this->client->Management->Account->returnAllAccounts($params);

        static::assertArrayHasKey('account_id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testReturnAnAccount(): void
    {
        $id = $this->faker->uuid;

        $this->mockResponse([
            'account_id'          => '123rvgm1iccsgnjj7nn6jwu1',
            'billing_state'       => 'paid',
            'email'               => 'example@example.com',
            'id'                  => '123rvgm1iccsgnjj7nn6jwu1',
            'provider'            => 'gmail',
            'sync_state'          => 'running',
            'trial'               => false,
            'authentication_type' => '',
        ]);

        $data = $this->client->Management->Account->returnAnAccount($id);

        static::assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testDeleteAnAccount(): void
    {
        $this->mockResponse([]);

        $id = $this->faker->uuid;

        $this->client->Management->Account->deleteAnAccount($id);

        $this->assertPassed();
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testReactiveAnAccount(): void
    {
        $this->mockResponse(['success' => 'true']);

        $id = $this->faker->uuid;

        $data = $this->client->Management->Account->reactiveAnAccount($id);

        static::assertArrayHasKey('success', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testRevokeAllTokens(): void
    {
        $this->mockResponse(['success' => 'true']);

        $id = $this->faker->uuid;

        $data = $this->client->Management->Account->revokeAllTokens($id);

        static::assertArrayHasKey('success', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
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

        static::assertArrayHasKey('state', $data);
    }

    // ------------------------------------------------------------------------------
}
