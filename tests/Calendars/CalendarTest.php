<?php

namespace Tests\Calendars;

use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Calendar Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/06/27
 *
 * @internal
 */
class CalendarTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testGetOAuthAuthorize(): void
    {
        $params =
        [
            'view' => 'count',
        ];

        $data = $this->client->Calendars()->Calendar()->getCalendarsList($params);

        $this->assertArrayHasKey('count', $data);
    }

    // ------------------------------------------------------------------------------

    public function testGetCalendar(): void
    {
        $id = 'f0yci053ovp2tit18hwemup33';

        $data = $this->client->Calendars()->Calendar()->getCalendar($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    public function testAddCalendar(): void
    {
        $params = $this->getCalendarInfo();

        $data = $this->client->Calendars()->Calendar()->addCalendar($params);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return array
     */
    private function getCalendarInfo(): array
    {
        return [
            'name'        => 'Test Calendar',
            'description' => 'This is a test calendar.',
            'timezone'    => 'America/New_York',
            'location'    => 'Front Conference Room',
        ];
    }
}
