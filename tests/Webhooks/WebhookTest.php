<?php

namespace Tests\Webhooks;

use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Webhook Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/22
 *
 * @internal
 */
class WebhookTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testReturnAllWebhooks(): void
    {
        $this->mockResponse([
            [
                'application_id' => '8eejdhpc5dv04w6ea8lzlxtkt',
                'callback_url'   => 'https://97a5db5e7c59.ngrok.io/webhook',
                'id'             => '7b5y8f25p344jy8yem6v5jir',
                'state'          => 'active',
                'triggers'       => ['message.created'],
                'version'        => '2.0',
            ],
        ]);

        $data = $this->client->Webhooks->Webhook->returnAllWebhooks();

        $this->assertArrayHasKey('version', $data[0]);
    }

    // ------------------------------------------------------------------------------

    public function testCreateAWebhook(): void
    {
        $para = [
            'state'        => 'inactive',
            'triggers'     => ['message.opened'],
            'callback_url' => 'http://www.test-nylas-api.com',
        ];

        $this->mockResponse([
            'application_id' => '8eejdhpc5dv04w6ea8lzlxtkt',
            'callback_url'   => 'https://97a5db5e7c59.ngrok.io/webhook',
            'id'             => '7b5y8f25p344jy8yem6v5jir',
            'state'          => 'active',
            'triggers'       => ['message.created'],
            'version'        => '2.0',
        ]);

        $data = $this->client->Webhooks->Webhook->createAWebhook($para);

        $this->assertArrayHasKey('version', $data);
    }

    // ------------------------------------------------------------------------------

    public function testUpdateAWebhook(): void
    {
        $id = '7b5y8f25p344jy8yem6v5jir';

        $this->mockResponse([
            'application_id' => '8eejdhpc5dv04w6ea8lzlxtkt',
            'callback_url'   => 'https://97a5db5e7c59.ngrok.io/webhook',
            'id'             => '7b5y8f25p344jy8yem6v5jir',
            'state'          => 'active',
            'triggers'       => ['message.created'],
            'version'        => '2.0',
        ]);

        $data = $this->client->Webhooks->Webhook->updateAWebhook($id);

        $this->assertArrayHasKey('version', $data);
    }

    // ------------------------------------------------------------------------------

    public function testReturnAWebhook(): void
    {
        $id = '7b5y8f25p344jy8yem6v5jir';

        $this->mockResponse([
            [
                'application_id' => '8eejdhpc5dv04w6ea8lzlxtkt',
                'callback_url'   => 'https://97a5db5e7c59.ngrok.io/webhook',
                'id'             => '7b5y8f25p344jy8yem6v5jir',
                'state'          => 'active',
                'triggers'       => ['message.created'],
                'version'        => '2.0',
            ],
        ]);

        $data = $this->client->Webhooks->Webhook->returnAWebhook($id);

        $this->assertArrayHasKey('version', $data[0]);
    }

    // ------------------------------------------------------------------------------

    public function testDeleteAWebhook(): void
    {
        $id = '7b5y8f25p344jy8yem6v5jir';

        $this->mockResponse([]);

        $this->client->Webhooks->Webhook->deleteAWebhook($id);

        $this->assertPassed();
    }

    // ------------------------------------------------------------------------------
}
