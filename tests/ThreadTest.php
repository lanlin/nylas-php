<?php namespace NylasTest;

/**
 * ----------------------------------------------------------------------------------
 * Thread Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2018/12/03
 */
class ThreadTest extends Abs
{

    // ------------------------------------------------------------------------------

    public function testGetThreadList()
    {
        $data = self::$api->Threads()->Thread()->getThreadsList();

        $this->assertTrue(count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testGetThread()
    {
        $id = '7ax24gg39w06rqosrda5dtw4w';

        $data = self::$api->Threads()->Thread()->getThread($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    public function testUpdateThread()
    {
        $params =
        [
            'id'     => '7ax24gg39w06rqosrda5dtw4w',
            'unread' => true
        ];

        $data = self::$api->Threads()->Thread()->updateThread($params);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testSearchThread()
    {
        $q = 'test@test.com';

        $data = self::$api->Threads()->Search()->threads($q);

        $this->assertTrue(count($data) > 0);
    }

    // ------------------------------------------------------------------------------

}
