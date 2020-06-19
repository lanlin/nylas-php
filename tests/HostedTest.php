<?php

namespace NylasTest;

/**
 * ----------------------------------------------------------------------------------
 * Hosted Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2020/04/26
 *
 * @internal
 */
class HostedTest extends Abs
{
    // ------------------------------------------------------------------------------

    public function testGetOAuthAuthorize(): void
    {
        $params =
        [
            'state'        => 'testing',
            'login_hint'   => 'test@gmail.com',
            'redirect_uri' => 'https://www.test.com/redirect_callback',
        ];

        $data = self::$api->Authentication()->Hosted()->getOAuthAuthorizeUrl($params);

        $this->assertTrue(\is_string($data));
    }

    // ------------------------------------------------------------------------------
}
