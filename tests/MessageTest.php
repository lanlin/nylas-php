<?php

namespace NylasTest;

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
class MessageTest extends Abs
{
    // ------------------------------------------------------------------------------

    public function testGetMessagesList(): void
    {
        $data = self::$api->Messages()->Message()->getMessagesList();

        $this->assertTrue(\count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testGetMessage(): void
    {
        $id = 'eyhcafxtzkke6tfsdo9g92utb';

        $data = self::$api->Messages()->Message()->getMessage($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    public function testGetRawMessage(): void
    {
        $id = 'eyhcafxtzkke6tfsdo9g92utb';

        $data = self::$api->Messages()->Message()->getRawMessage($id);

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

        $data = self::$api->Messages()->Message()->updateMessage($params);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testSearchMessage(): void
    {
        $q = '测试';

        $data = self::$api->Messages()->Search()->messages($q);

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

        $data = self::$api->Messages()->Sending()->sendDirectly($params);

        $this->assertArrayHasKey('id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    public function testSendRaw(): void
    {
        $content = '';

        $data = self::$api->Messages()->Sending()->sendRawMIME($content);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------
}
