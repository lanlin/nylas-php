<?php

namespace Nylas\Tests;

use Throwable;

/**
 * ----------------------------------------------------------------------------------
 * Webhook Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2020/04/26
 *
 * @internal
 */
class WebhookTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testGetWebhookList(): void
    {
        $data = $this->client->Webhooks()->Webhook()->getWebhookList();

        $this->assertTrue(\count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testCreateWebhook(): void
    {
        $para =
        [
            'state'        => 'inactive',
            'triggers'     => ['message.opened'],
            'callback_url' => 'http://www.test-nylas-api.com',
        ];

        $data = $this->client->Webhooks()->Webhook()->createWebhook($para);

        $this->assertArrayHasKey('id', $data);

        $this->testGetWebhook($data['id']);
        $this->testUpdateWebhook($data['id']);
    }

    // ------------------------------------------------------------------------------

    public function testUpdateWebhook($id): void
    {
        $data = $this->client->Webhooks()->Webhook()->updateWebhook($id);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testGetWebhook($id): void
    {
        $data = $this->client->Webhooks()->Webhook()->getWebhook($id);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testDeleteWebhook($id): void
    {
        try
        {
            $this->client->Webhooks()->Webhook()->deleteWebhook($id);
            $this->assertTrue(true);
        }
        catch (Throwable $e)
        {
            $this->assertTrue(false);
        }
    }

    // ------------------------------------------------------------------------------
}
