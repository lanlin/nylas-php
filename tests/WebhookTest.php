<?php namespace NylasTest;

/**
 * ----------------------------------------------------------------------------------
 * Webhook Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2018/12/03
 */
class WebhookTest extends Abs
{

    // ------------------------------------------------------------------------------

    public function testGetWebhookList()
    {
        $data = self::$api->Webhooks()->Webhook()->getWebhookList();

        $this->assertTrue(count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testGetWebhook()
    {
        $id = '7ax24gg39w06rqosrda5dtw4w';

        $data = self::$api->Webhooks()->Webhook()->getWebhook($id);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

}
