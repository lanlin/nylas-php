<?php

namespace Tests\Authentication;

use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Hosted Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2020/06/24
 *
 * @internal
 */
class HostedTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testGetOAuthAuthorize(): void
    {
        $params =
         [
             'state'         => 'testing',
             'scopes'        => 'email,contacts,calendar',
             'login_hint'    => 'test@gmail.com',
             'redirect_uri'  => 'https://www.test.com/redirect_callback',
             'response_type' => 'code',
         ];

        $data = $this->client->Authentication()->Hosted()->getOAuthAuthorizeUrl($params);

        $this->assertTrue(\is_string($data));
    }

    // ------------------------------------------------------------------------------

    public function testPostOAuthToken(): void
    {
        $code = 'sfjalgjaldgjl';

        $this->mockResponse([
            'client_id'      => 'dfasdflasd',
            'client_secret'  => 'sdflajdlf',
            'grant_type'     => 'authorization_code',
            'code'           => 'fadfldglajl',
        ]);

        $data = $this->client->Authentication()->Hosted()->postOAuthToken($code);

        $this->assertTrue(!empty($data['client_id']));
    }

    // ------------------------------------------------------------------------------

    public function testPostOAuthRevoke(): void
    {
        $this->mockResponse(['success' => true]);

        $data = $this->client->Authentication()->Hosted()->postOAuthRevoke();

        $this->assertTrue($data['success']);
    }

    // ------------------------------------------------------------------------------
}
