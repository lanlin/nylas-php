<?php

declare(strict_types = 1);

namespace Tests\Calendars;

use JsonException;
use Tests\AbsCase;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Calendar Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 *
 * @internal
 */
class CalendarTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testReturnAllCalendars(): void
    {
        $params = [
            'view'   => 'expanded',
            'limit'  => 10,
            'offset' => 0,
        ];

        $this->mockResponse([
            [
                'account_id'  => '5tgncdmczat03416u7d6uypyi',
                'description' => 'Emailed events',
                'id'          => '6d4d54fd53c54a70a2b98e36038d',
                'is_primary'  => null,
                'location'    => null,
                'name'        => 'Emailed events',
                'object'      => 'calendar',
                'read_only'   => true,
                'timezone'    => null,
            ],
            [
                'account_id'  => '5tgncdmczat03416u7d6uypyi',
                'description' => null,
                'id'          => 'fe51c4d8bedf45ec949bf1033c7',
                'is_primary'  => true,
                'location'    => null,
                'name'        => 'tatiana.p@nylas.com',
                'object'      => 'calendar',
                'read_only'   => false,
                'timezone'    => 'America/Chicago',
            ],
        ]);

        $data = $this->client->Calendars->Calendar->returnAllCalendars($params);

        static::assertArrayHasKey('account_id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testCreateACalendar(): void
    {
        $params = [
            'name'        => 'My New Calendar',
            'description' => 'Description of my new calendar',
            'location'    => 'Location description',
            'timezone'    => 'America/Los_Angeles',
        ];

        $this->mockResponse([
            'account_id'    => 'eof2wrhqkl7kdwhy9hylpv9o9',
            'description'   => 'Description of my new calendar',
            'id'            => '8e570s302fdazx9zqwiuk9jqn',
            'is_primary'    => true,
            'job_status_id' => '48pp6ijzrxpw9jors9ylnsxnf',
            'location'      => 'Location description',
            'name'          => 'My New Calendar',
            'object'        => 'calendar',
            'read_only'     => true,
            'timezone'      => 'America/Los_Angeles',
            'hex_color'     => '#9cbf8b',
        ]);

        $data = $this->client->Calendars->Calendar->createACalendar($params);

        static::assertArrayHasKey('account_id', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws JsonException
     */
    public function testReturnACalendar(): void
    {
        $id = '6d4d54fd53c54a70a2b98e36038d';

        $this->mockResponse([
            'account_id'  => '5tgncdmczat03416u7d6uypyi',
            'description' => 'Emailed events',
            'id'          => '6d4d54fd53c54a70a2b98e36038d',
            'is_primary'  => null,
            'location'    => null,
            'name'        => 'Emailed events',
            'object'      => 'calendar',
            'read_only'   => true,
            'timezone'    => null,
            'hex_color'   => '#9cbf8b',
        ]);

        $data = $this->client->Calendars->Calendar->returnACalendar($id);

        static::assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testUpdateACalendar(): void
    {
        $id = '8e570s302fdazx9zqwiuk9jqn';

        $params = [
            'name'        => 'My New Calendar',
            'description' => 'Description of my new calendar',
            'location'    => 'Location description',
            'timezone'    => 'America/Los_Angeles',
        ];

        $this->mockResponse([
            'account_id'    => 'eof2wrhqkl7kdwhy9hylpv9o9',
            'description'   => 'Description of my new calendar',
            'id'            => '8e570s302fdazx9zqwiuk9jqn',
            'is_primary'    => true,
            'job_status_id' => '48pp6ijzrxpw9jors9ylnsxnf',
            'location'      => 'Location description',
            'name'          => 'My New Calendar',
            'object'        => 'calendar',
            'read_only'     => true,
            'timezone'      => 'America/Los_Angeles',
            'hex_color'     => '#9cbf8b',
        ]);

        $data = $this->client->Calendars->Calendar->updateACalendar($id, $params);

        static::assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws JsonException
     */
    public function testDeleteACalendar(): void
    {
        $id = '8e570s302fdazx9zqwiuk9jqn';

        $this->mockResponse(['job_status_id' => '48pp6ijzrxpw9jors9ylnsxnf']);

        $data = $this->client->Calendars->Calendar->deleteACalendar($id);

        static::assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testCalendarFreeOrBusy(): void
    {
        $params = [
            'emails'     => ['nyla@nylas.com'],
            'end_time'   => 1409598000,
            'start_time' => 1409594400,
        ];

        $this->mockResponse([[
            'object'     => 'free_busy',
            'email'      => 'swag@nylas.com',
            'time_slots' => [
                [
                    'object'     => 'time_slot',
                    'status'     => 'busy',
                    'start_time' => 1409594400,
                    'end_time'   => 1409598000,
                ],
                [
                    'object'     => 'time_slot',
                    'status'     => 'busy',
                    'start_time' => 1409598000,
                    'end_time'   => 1409599000,
                ],
            ],
        ]]);

        $data = $this->client->Calendars->Calendar->calendarFreeOrBusy($params);

        static::assertArrayHasKey('email', $data[0]);
    }

    // ------------------------------------------------------------------------------
}
