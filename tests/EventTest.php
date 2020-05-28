<?php namespace NylasTest;

/**
 * ----------------------------------------------------------------------------------
 * Event Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2020/04/26
 */
class EventTest extends Abs
{

    // ------------------------------------------------------------------------------

    public function testGetEventList() : void
    {
        $data = self::$api->Events()->Event()->getEventsList();

        $this->assertTrue(count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testGetEvent() : void
    {
        $params = ['id' => 'ejom4k3o5qor5ooyh8yx7hgbw'];

        $data = self::$api->Events()->Event()->getEvent($params);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testAddEvent() : void
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

    public function testUpdateEvent() : void
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

    public function testDeleteDraft() : void
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
