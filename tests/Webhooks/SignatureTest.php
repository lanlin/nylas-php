<?php

declare(strict_types = 1);

namespace Tests\Webhooks;

use function json_encode;

use Exception;
use JsonException;
use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Webhook Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
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

    /**
     * @throws JsonException
     */
    public function testParseNotification(): void
    {
        $params = json_encode(['deltas' => ['aaa' => 'bbb']], JSON_THROW_ON_ERROR);

        $data = $this->client->Webhooks->Signature->parseNotification($params);

        static::assertArrayHasKey('aaa', $data);
    }

    // ------------------------------------------------------------------------------

    public function testXSignatureVerification(): void
    {
        $para = $this->faker->name;
        $code = $this->faker->uuid;

        $data = $this->client->Webhooks->Signature->xSignatureVerification($code, $para);

        static::assertFalse($data);
    }

    // ------------------------------------------------------------------------------
}
