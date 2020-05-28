<?php namespace NylasTest;

/**
 * ----------------------------------------------------------------------------------
 * Webhook Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2020/04/26
 */
class WebhookTest extends Abs
{

    // ------------------------------------------------------------------------------

    public function testGetWebhookList() : void
    {
        $data = self::$api->Webhooks()->Webhook()->getWebhookList();

        $this->assertTrue(count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testCreateWebhook() : void
    {
        $para =
        [
            'state'        => 'inactive',
            'triggers'     => ['message.opened'] ,
            'callback_url' => 'http://www.test-nylas-api.com',
        ];

        $data = self::$api->Webhooks()->Webhook()->createWebhook($para);

        $this->assertArrayHasKey('id', $data);

        $this->testGetWebhook($data['id']);
        $this->testUpdateWebhook($data['id']);
    }

    // ------------------------------------------------------------------------------

    public function testUpdateWebhook($id) : void
    {
        $data = self::$api->Webhooks()->Webhook()->updateWebhook($id);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testGetWebhook($id) : void
    {
        $data = self::$api->Webhooks()->Webhook()->getWebhook($id);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testDeleteWebhook($id) : void
    {
        try
        {
            self::$api->Webhooks()->Webhook()->deleteWebhook($id);
            $this->assertTrue(true);
        }
        catch (\Throwable $e)
        {
            $this->assertTrue(false);
        }
    }

    // ------------------------------------------------------------------------------

}
