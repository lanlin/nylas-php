<?php namespace NylasTest;

/**
 * ----------------------------------------------------------------------------------
 * Event Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2018/11/30
 */
class EventTest extends Abs
{

    // ------------------------------------------------------------------------------

    public function testGetEventList()
    {
        $data = self::$api->Events()->Event()->getEventsList();

        $this->assertTrue(count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testGetEvent()
    {
        $params = ['id' => 'ejom4k3o5qor5ooyh8yx7hgbw'];

        $data = self::$api->Events()->Event()->getEvent($params);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testAddEvent()
    {
        $params =
        [
            'calendar_id' => '1fskeosmvaffwuffq774enx5p',
            'when'        => ['time' => time()],
            'title'       => 'nothing...',
        ];

        $data = self::$api->Events()->Event()->addEvent($params);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testUpdateEvent()
    {
        $params =
        [
            'id'      => '47137b6urkg0cf738o7is2aa3',
            'when'    => ['time' => time()]
        ];

        $data = self::$api->Events()->Event()->updateEvent($params);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testDeleteDraft()
    {
        $params =
        [
            'id' => '47137b6urkg0cf738o7is2aa3',
        ];

        try
        {
            $back = true;
            self::$api->Events()->Event()->deleteEvent($params);
        }
        catch (\Exception $e)
        {
            $back = false;
        }

        $this->assertTrue($back);
    }

    // ------------------------------------------------------------------------------

}
