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

    public function testGetWebhook() : void
    {
        $id = '7ax24gg39w06rqosrda5dtw4w';

        $data = self::$api->Webhooks()->Webhook()->getWebhook($id);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

}
