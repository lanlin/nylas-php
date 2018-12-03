<?php namespace NylasTest;

/**
 * ----------------------------------------------------------------------------------
 * Message Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2018/12/03
 */
class MessageTest extends Abs
{

    // ------------------------------------------------------------------------------

    public function testGetMessagesList()
    {
        $data = self::$api->Messages()->Message()->getMessagesList();

        $this->assertTrue(count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testGetMessage()
    {
        $id = 'eyhcafxtzkke6tfsdo9g92utb';

        $data = self::$api->Messages()->Message()->getMessage($id);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testGetRawMessage()
    {
        $id = 'eyhcafxtzkke6tfsdo9g92utb';

        $data = self::$api->Messages()->Message()->getRawMessage($id);

        $this->assertTrue(is_object($data));
    }

    // ------------------------------------------------------------------------------

    public function testUpdateMessage()
    {
        $params =
        [
            'id'     => 'eyhcafxtzkke6tfsdo9g92utb',
            'unread' => false
        ];

        $data = self::$api->Messages()->Message()->updateMessage($params);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testSearchMessage()
    {
        $q = '测试';

        $data = self::$api->Messages()->Search()->messages($q);

        $this->assertTrue(count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testSendMessage()
    {
        $params =
        [
            'to'      => [['email' => 'test@test.com']],
            'subject' => 'this is for test'
        ];

        $data = self::$api->Messages()->Sending()->sendDirectly($params);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testSendRaw()
    {
        $content = '';

        $data = self::$api->Messages()->Sending()->sendRawMIME($content);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

}
