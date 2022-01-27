<?php

namespace Tests\Webhooks;

use Exception;
use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Webhook Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2022/01/27
 *
 * @internal
 */
class SignatureTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testEchoChallenge(): void
    {
        $this->client->Webhooks->Signature->echoChallenge();

        $this->assertPassed();
    }

    // ------------------------------------------------------------------------------

    public function testGetNotification(): void
    {
        $this->expectException(Exception::class);

        $this->client->Webhooks->Signature->getNotification();
    }

    // ------------------------------------------------------------------------------

    public function testParseNotification(): void
    {
        $params = \json_encode(['deltas' => ['aaa' => 'bbb']], JSON_THROW_ON_ERROR);

        $data = $this->client->Webhooks->Signature->parseNotification($params);

        $this->assertArrayHasKey('aaa', $data);
    }

    // ------------------------------------------------------------------------------

    public function testXSignatureVerification(): void
    {
        $para = $this->faker->name;
        $code = $this->faker->uuid;

        $data = $this->client->Webhooks->Signature->xSignatureVerification($code, $para);

        $this->assertFalse($data);
    }

    // ------------------------------------------------------------------------------
}
