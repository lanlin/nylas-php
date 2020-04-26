<?php namespace NylasTest;

/**
 * ----------------------------------------------------------------------------------
 * Calendar Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2020/04/26
 */
class CalendarTest extends Abs
{

    // ------------------------------------------------------------------------------

    public function testGetOAuthAuthorize() : void
    {
        $params =
        [
            'view' => 'count',
        ];

        $data = self::$api->Calendars()->Calendar()->getCalendarsList($params);

        $this->assertArrayHasKey('count', $data);
    }

    // ------------------------------------------------------------------------------

    public function testGetCalendar() : void
    {
        $id = 'f0yci053ovp2tit18hwemup33';

        $data = self::$api->Calendars()->Calendar()->getCalendar($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

}
