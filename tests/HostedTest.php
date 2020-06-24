<?php

namespace Nylas\Tests;

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

    /*
     *  $data
     *
     * [httpStatus] => 200
     * [invalidJson] => 1
     * [contentType] => text/html; charset=utf-8
     * [contentBody] =>
     */
    // public function testPostOAuthRevoke(): void
    // {
    //     $data = $this->client->Authentication()->Hosted()->postOAuthRevoke();

    //     $this->assertTrue(\is_string($data));
    // }

    // ------------------------------------------------------------------------------
}
