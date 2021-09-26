<?php

namespace Tests\Calendars;

use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Calendar Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/26
 *
 * @internal
 */
class AvailabilityTest extends AbsCase
{
    // ------------------------------------------------------------------------------

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
                [
                    'end'    => 1605805800,
                    'object' => 'time_slot',
                    'start'  => 1605804000,
                    'status' => 'free',
                ],
                [
                    'end'    => 1605806400,
                    'object' => 'time_slot',
                    'start'  => 1605804600,
                    'status' => 'free',
                ],
                [
                    'end'    => 1605807000,
                    'object' => 'time_slot',
                    'start'  => 1605805200,
                    'status' => 'free',
                ],
                [
                    'end'    => 1605816000,
                    'object' => 'time_slot',
                    'start'  => 1605814200,
                    'status' => 'free',
                ],
            ],
        ]);

        $data = $this->client->Calendars->Availability->availabilityForASingleMeeting($params);

        $this->assertArrayHasKey('time_slots', $data);
    }

    // ------------------------------------------------------------------------------

    private function getSingleParams(): array
    {
        return [
            'duration_minutes' => 30,
            'start_time'       => 1605794400,
            'end_time'         => 1605826800,
            'interval_minutes' => 10,
            'emails'           => ['swag@nylas.com'],
            'free_busy' => [
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
                    'days' => ['0'],
                    'emails' => ['swag@nylas.com'],
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
