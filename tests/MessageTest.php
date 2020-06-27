<?php

namespace Nylas\Tests;

/**
 * ----------------------------------------------------------------------------------
 * Message Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2020/04/26
 *
 * @internal
 */
class MessageTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testGetMessagesList(): void
    {
        $data = $this->client->Messages()->Message()->getMessagesList();

        $this->assertTrue(\count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testGetMessage(): void
    {
        $id = 'eyhcafxtzkke6tfsdo9g92utb';

        $data = $this->client->Messages()->Message()->getMessage($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    public function testGetRawMessage(): void
    {
        $id = 'eyhcafxtzkke6tfsdo9g92utb';

        $data = $this->client->Messages()->Message()->getRawMessage($id);

        $this->assertTrue(\is_object($data));
    }

    // ------------------------------------------------------------------------------

    public function testUpdateMessage(): void
    {
        $params =
        [
            'id'     => 'eyhcafxtzkke6tfsdo9g92utb',
            'unread' => false,
        ];

        $data = $this->client->Messages()->Message()->updateMessage($params);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testSearchMessage(): void
    {
        $q = 'testing';

        $data = $this->client->Messages()->Search()->messages($q);

        $this->assertTrue(\count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testSendMessage(): void
    {
        $params =
        [
            'to'      => [['email' => 'test@test.com']],
            'subject' => 'this is for test',
        ];

        $data = $this->client->Messages()->Sending()->sendDirectly($params);

        $this->assertArrayHasKey('id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    public function testSendRaw(): void
    {
        $content = 'testing send raw';

        $data = $this->client->Messages()->Sending()->sendRawMIME($content);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------
}
