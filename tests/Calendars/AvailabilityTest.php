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
class AvailabilityTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testAvailabilityForASingleMeeting(): void
    {
        $params = $this->getSingleParams();

        $this->mockResponse([
            'object'     => 'availability',
            'time_slots' => [
                [
                    'end'    => 1605803400,
                    'object' => 'time_slot',
                    'start'  => 1605801600,
                    'status' => 'free',
                ],
                [
                    'end'    => 1605804000,
                    'object' => 'time_slot',
                    'start'  => 1605802200,
                    'status' => 'free',
                ],
                [
                    'end'    => 1605804600,
                    'object' => 'time_slot',
                    'start'  => 1605802800,
                    'status' => 'free',
                ],
                [
                    'end'    => 1605805200,
                    'object' => 'time_slot',
                    'start'  => 1605803400,
                    'status' => 'free',
                ],
            ],
        ]);

        $data = $this->client->Calendars->Availability->availabilityForASingleMeeting($params);

        static::assertArrayHasKey('time_slots', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testAvailabilityForMultipleMeetings(): void
    {
        $params = $this->getMultipleParams();

        $this->mockResponse(
            [
                [
                    [
                        'emails' => [
                            'kat@spacetech.com',
                            'dorothy@spacetech.com',
                        ],
                        'end_time'   => 1605794400,
                        'start_time' => 1605792600,
                    ],
                    [
                        'emails' => [
                            'dorothy@spacetech.com',
                        ],
                        'end_time'   => 1605796200,
                        'start_time' => 1605794400,
                    ],
                ],
                [
                    [
                        'emails' => [
                            'dorothy@spacetech.com',
                        ],
                        'end_time'   => 1605801600,
                        'start_time' => 1605799800,
                    ],
                    [
                        'emails' => [
                            'kat@spacetech.com',
                            'dorothy@spacetech.com',
                        ],
                        'end_time'   => 1605803400,
                        'start_time' => 1605801600,
                    ],
                ],
            ]
        );

        $data = $this->client->Calendars->Availability->availabilityForMultipleMeetings($params);

        static::assertCount(2, $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return int[]
     */
    private function getSingleParams(): array
    {
        return [
            'duration_minutes' => 30,
            'start_time'       => 1605794400,
            'end_time'         => 1605826800,
            'interval_minutes' => 10,
            'emails'           => ['swag@nylas.com'],
            'free_busy'        => [
                [
                    'email'      => 'lamarr@player.com',
                    'object'     => 'free/busy',
                    'time_slots' => [
                        [
                            'start_time' => 1601042400,
                            'end_time'   => 1601044200,
                            'object'     => 'time_slot',
                            'status'     => 'busy',
                        ],
                    ],
                ],
            ],
            'open_hours' => [
                [
                    'days'        => ['0'],
                    'emails'      => ['swag@nylas.com'],
                    'timezone'    => 'America/Chicago',
                    'start'       => '10:00',
                    'end'         => '14:00',
                    'object_type' => 'open_hours',
                ],
            ],
        ];
    }

    // ------------------------------------------------------------------------------

    /**
     * @return int[]
     */
    private function getMultipleParams(): array
    {
        return [
            'duration_minutes' => 30,
            'start_time'       => 1605794400,
            'end_time'         => 1605826800,
            'interval_minutes' => 10,
            'emails'           => [['swag@nylas.com']],
            'free_busy'        => [
                [
                    'email'      => 'swag@nylas.com',
                    'object'     => 'free_busy',
                    'time_slots' => [
                        [
                            'start_time' => 1605819600,
                            'end_time'   => 1605821400,
                            'object'     => 'time_slot',
                            'status'     => 'busy',
                        ],
                    ],
                ],
            ],
            'open_hours' => [
                [
                    'emails'      => [['swag@nylas.com']],
                    'days'        => ['0'],
                    'timezone'    => 'America/Chicago',
                    'start'       => '10:00',
                    'end'         => '14:00',
                    'object_type' => 'open_hours',
                ],
            ],
        ];
    }

    // ------------------------------------------------------------------------------
}
