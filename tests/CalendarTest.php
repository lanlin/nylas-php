<?php

namespace Nylas\Tests;

/**
 * ----------------------------------------------------------------------------------
 * Calendar Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
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

    public function testUpdateCalendar(): void
    {
        $data = $this->client->Calendars()->Calendar()->addCalendar($this->getCalendarInfo());

        $params =
            [
                'id'          => $data['id'],
                'name'        => 'Different Calendar',
                'description' => 'This is now a different calendar.',
                'timezone'    => 'America/Los_Angeles',
            ];

        $data = $this->client->Calendars()->Calendar()->updateContact($params);

        $this->assertEquals($data['id'], $params['id']);
        $this->assertEquals($data['name'], $params['name']);
        $this->assertEquals($data['description'], $params['description']);
        $this->assertEquals($data['timezone'], $params['timezone']);
    }

    // ------------------------------------------------------------------------------

    public function testDeleteCalendar(): void
    {
        $data = $this->client->Calendars()->Calendar()->addCalendar($this->getCalendarInfo());

        try
        {
            $back = true;
            $this->client->Calendars()->Calendar()->deleteCalendar($data['id']);
        }
        catch (Exception $e)
        {
            $back = false;
        }

        $this->assertTrue($back);
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
