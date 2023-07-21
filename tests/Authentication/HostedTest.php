<?php

declare(strict_types = 1);

namespace Tests\Authentication;

use JsonException;
use Tests\AbsCase;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Hosted Test
 * ----------------------------------------------------------------------------------
 *
 * @see https://developer.nylas.com/docs/api/#tag--Hosted-Authentication
 *
 * @author lanlin
 * @change 2023/07/21
 *
 * @internal
 */
class HostedTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testAuthenticateUser(): void
    {
        $params = [
            'state'         => 'testing',
            'scopes'        => 'email,contacts,calendar',
            'login_hint'    => $this->faker->email,
            'redirect_uri'  => $this->faker->url,
            'response_type' => 'code',
        ];

        $data = $this->client->Authentication->Hosted->authenticateUser($params);

        static::assertIsString($data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testSendAuthorizationCode(): void
    {
        $code = $this->faker->postcode;

        $this->mockResponse([
            'provider'      => 'gmail',
            'token_type'    => 'bearer',
            'account_id'    => $this->faker->md5,
            'access_token'  => $this->faker->md5,
            'email_address' => $this->faker->email,
        ]);

        $data = $this->client->Authentication->Hosted->sendAuthorizationCode($code);

        static::assertNotEmpty($data['access_token']);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testRevokeAccessTokens(): void
    {
        $this->mockResponse(['success' => 'true']);

        $data = $this->client->Authentication->Hosted->revokeAccessTokens();

        static::assertNotEmpty($data['success']);
    }

    // ------------------------------------------------------------------------------
}
