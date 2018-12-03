<?php namespace NylasTest;

/**
 * ----------------------------------------------------------------------------------
 * Calendar Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2018/11/28
 */
class CalendarTest extends Abs
{

    // ------------------------------------------------------------------------------

    public function testGetOAuthAuthorize()
    {
        $params =
        [
            'view' => 'count',
        ];

        $data = self::$api->Calendars()->Calendar()->getCalendarsList($params);

        $this->assertArrayHasKey('count', $data);
    }

    // ------------------------------------------------------------------------------

    public function testGetCalendar()
    {
        $id = 'f0yci053ovp2tit18hwemup33';

        $data = self::$api->Calendars()->Calendar()->getCalendar($id);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

}
