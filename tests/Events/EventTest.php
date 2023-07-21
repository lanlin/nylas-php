<?php

declare(strict_types = 1);

namespace Tests\Events;

use JsonException;
use Tests\AbsCase;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Event Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 *
 * @internal
 */
class EventTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testReturnAllEvents(): void
    {
        $this->mockResponse([$this->getEventData()]);

        $data = $this->client->Events->Event->returnAllEvents();

        static::assertArrayHasKey('account_id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    /**
     * @dataProvider getProvidData
     *
     * @param array $params
     *
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testCreateAnEvents(array $params): void
    {
        $this->mockResponse($this->getEventData());

        $data = $this->client->Events->Event->createAnEvent($params);

        static::assertArrayHasKey('account_id', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws JsonException
     */
    public function testReturnAnEvent(): void
    {
        $id = '{event_id}';

        $this->mockResponse($this->getEventData());

        $data = $this->client->Events->Event->returnAnEvent($id);

        static::assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @dataProvider getProvidData
     *
     * @param array $params
     *
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testUpdateAnEvent(array $params): void
    {
        $id = '{event_id}';

        $this->mockResponse($this->getEventData());

        $data = $this->client->Events->Event->updateAnEvent($id, $params);

        static::assertArrayHasKey('account_id', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws JsonException
     */
    public function testDeleteAnEvent(): void
    {
        $id = '{event_id}';

        $this->mockResponse([]);

        $data = $this->client->Events->Event->deleteAnEvent($id);

        static::assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testSendRSVP(): void
    {
        $params = [
            'event_id'   => 'string',
            'status'     => 'yes',
            'account_id' => 'string',
        ];

        $this->mockResponse($this->getEventData());

        $data = $this->client->Events->Event->sendRSVP($params);

        static::assertArrayHasKey('account_id', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testGenerateICSFile(): void
    {
        $params = [
            'event_id'    => '<EVENT_ID>',
            'ics_options' => [
                'ical_uid' => 'string',
                'method'   => 'request',
                'prodid'   => 'string',
            ],
        ];

        $this->mockResponse([
            'ics' => "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//Acme//Generated-ICS\r\nCALSCALE:GREGORIAN\r\nMETHOD:REQUEST\r\nBEGIN:VEVENT\r\nSUMMARY:Example Event\r\nDTSTART;VALUE=DATE-TIME:20211117T200000Z\r\nDTEND;VALUE=DATE-TIME:20211117T203000Z\r\nDTSTAMP;VALUE=DATE-TIME:20211117T194505Z\r\nUID:globally_unique_no_spaces_0\r\nATTENDEE;CN=\"Tippy Hedren\";CUTYPE=INDIVIDUAL;PARTSTAT=NEEDS-ACTION;ROLE\r\n =REQ-PARTICIPANT;RSVP=TRUE;SCHEDULE-AGENT=SERVER:mailto:thedrenv@outlook.c\r\n om\r\nLAST-MODIFIED;VALUE=DATE-TIME:20211117T194457Z\r\nLOCATION:Coffee Shop\r\nORGANIZER:c_a05n8d55tc8k8h0ndn0kdehcrs@group.calendar.google.com\r\nSTATUS:CONFIRMED\r\nTRANSP:OPAQUE\r\nEND:VEVENT\r\nEND:VCALENDAR\r\n",
        ]);

        $data = $this->client->Events->Event->generateICSFile($params);

        static::assertArrayHasKey('ics', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return array[]
     */
    public static function getProvidData(): array
    {
        $event = [
            'title'        => 'Birthday Party',
            'calendar_id'  => '947kpa7ih22bfkeujpkfqn5bu',
            'busy'         => true,
            'read_only'    => true,
            'participants' => [
                [
                    'name'   => 'Aristotle',
                    'email'  => 'aristotle@nylas.com',
                    'status' => 'yes',
                ],
            ],
            'description' => 'Come ready to skate',
            'when'        => [
                'time'     => 1408875644,
                'timezone' => 'America/New_York',
            ],
            'location'   => 'Roller Rink',
            'recurrence' => [
                'rrule' => [
                    'RRULE:FREQ=WEEKLY;BYDAY=MO',
                ],
                'timezone' => 'America/New_York',
            ],
        ];

        $conference = [
            'title'        => 'Birthday Party',
            'calendar_id'  => '947kpa7ih22bfkeujpkfqn5bu',
            'busy'         => true,
            'read_only'    => true,
            'participants' => [
                [
                    'name'   => 'Dorothy Vaughan',
                    'email'  => 'dorothy@spacetech.com',
                    'status' => 'noreply',
                ],
            ],
            'description' => 'Come ready to skate',
            'when'        => [
                'time'     => 1408875644,
                'timezone' => 'America/New_York',
            ],
            'location'   => 'Roller Rink',
            'recurrence' => [
                'rrule' => [
                    'RRULE:FREQ=WEEKLY;BYDAY=MO',
                ],
                'timezone' => 'America/New_York',
            ],
            'conferencing' => [
                'provider' => 'WebEx',
                'details'  => [
                    'password' => 'string',
                    'pin'      => 'string',
                    'url'      => 'string',
                ],
            ],
            'reminder_minutes' => '[20]',
            'reminder_method'  => 'popup',
        ];

        $metadata = [
            'title'        => 'Birthday Party',
            'location'     => 'Roller Rink',
            'calendar_id'  => '{calendar_id}',
            'busy'         => true,
            'read_only'    => false,
            'participants' => [
                [
                    'name'  => 'Thomas Edison',
                    'email' => 'tom@brightideas.com',
                ],
            ],
            'description' => 'Lets Party!!!',
            'when'        => [
                'start_time' => 1615330800,
                'end_time'   => 1615334400,
            ],
            'metadata' => [
                'number_of_guests'  => '55',
                'event_type'        => 'birthday',
                'internal_event_id' => 'b55469dk',
            ],
        ];

        $zoom = [
            'title'        => 'Birthday Party',
            'location'     => 'Roller Rink',
            'calendar_id'  => 'egtdopqam5jxky7ifrkwcra55',
            'busy'         => true,
            'read_only'    => false,
            'conferencing' => [
                'provider'   => 'Zoom Meeting',
                'autocreate' => [
                    'settings' => [
                        'password' => '6789011',
                        'settings' => [
                            'mute_upon_entry' => true,
                        ],
                    ],
                ],
            ],
            'participants' => [
                [
                    'name'  => 'Katherine Johnson',
                    'email' => 'kat@spacetech.com',
                ],
            ],
            'description' => 'Lets celebrate',
            'when'        => [
                'start_time' => 1627499520,
                'end_time'   => 1630245600,
            ],
        ];

        $notification = [
            'title'        => 'Lets celebrate',
            'location'     => 'Roller Rink',
            'calendar_id'  => 'egtdopqam5jxky7ifrkwcra55',
            'busy'         => true,
            'read_only'    => false,
            'conferencing' => [
                'provider'   => 'Zoom Meeting',
                'autocreate' => [
                    'settings' => ['settings' => []],
                ],
            ],
            'participants' => [
                [
                    'name'  => 'Katherine Johnson',
                    'email' => 'kat@spacetech.com',
                ],
            ],
            'when' => [
                'start_time' => 1627499520,
                'end_time'   => 1630245600,
            ],
            'notifications' => [
                [
                    'type'                  => 'email',
                    'minutes_before_events' => '600',
                    'subject'               => 'Test Event Notification',
                    'body'                  => 'Reminding you about our meeting.',
                ],
                [
                    'type'                  => 'sms',
                    'minutes_before_events' => '60',
                    'message'               => 'Test Event Notfication',
                ],
            ],
        ];

        return [
            'zoom'         => [$zoom],
            'event'        => [$event],
            'metadata'     => [$metadata],
            'conference'   => [$conference],
            'notification' => [$notification],
        ];
    }

    // ------------------------------------------------------------------------------

    /**
     * @return array
     */
    private function getEventData(): array
    {
        return [
            'account_id'          => '{account_id}',
            'busy'                => true,
            'calendar_id'         => '{calendar_id}',
            'description'         => 'Coffee meeting',
            'ical_uid'            => '{ical_uid}',
            'id'                  => '{event_id}',
            'location'            => 'string',
            'message_id'          => 'string',
            'object'              => 'event',
            'owner'               => '<some_email@email.com>',
            'original_start_time' => '',
            'participants'        => [
                [
                    'name'    => 'Dorothy Vaughan',
                    'email'   => 'dorothy@spacetech.com',
                    'status'  => 'noreply',
                    'comment' => 'string',
                ],
            ],
            'read_only' => true,
            'title'     => 'Remote Event: Group Yoga Class',
            'when'      => [
                'start_time'     => 1409594400,
                'end_time'       => 1409598000,
                'start_timezone' => 'America/New_York',
                'end_timezone'   => 'America/New_York',
            ],
            'status'       => 'confirmed',
            'conferencing' => [
                'provider' => 'WebEx',
                'details'  => [
                    'password' => 'string',
                    'pin'      => 'string',
                    'url'      => 'string',
                ],
            ],
            'job_status_id' => 'string',
            'recurrence'    => [
                'rrule' => [
                    'RRULE:FREQ=WEEKLY;BYDAY=MO',
                ],
                'timezone' => 'America/New_York',
            ],
            'metadata' => [
                'your-key' => 'string',
            ],
        ];
    }

    // ------------------------------------------------------------------------------
}
