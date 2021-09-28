<?php

namespace Tests\Events;

use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Event Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/22
 *
 * @internal
 */
class EventTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testReturnAllEvents(): void
    {
        $this->mockResponse([$this->getEventData()]);

        $data = $this->client->Events->Event->returnAllEvents();

        $this->assertArrayHasKey('account_id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    /**
     * @dataProvider getProvidData
     *
     * @param array $params
     */
    public function testCreateAnEvents(array $params): void
    {
        $this->mockResponse($this->getEventData());

        $data = $this->client->Events->Event->createAnEvent($params);

        $this->assertArrayHasKey('account_id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testReturnAnEvent(): void
    {
        $id = '{event_id}';

        $this->mockResponse($this->getEventData());

        $data = $this->client->Events->Event->returnAnEvent($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @dataProvider getProvidData
     *
     * @param array $params
     */
    public function testUpdateAnEvent(array $params): void
    {
        $id = '{event_id}';

        $this->mockResponse($this->getEventData());

        $data = $this->client->Events->Event->updateAnEvent($id, $params);

        $this->assertArrayHasKey('account_id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testDeleteAnEvent(): void
    {
        $id = '{event_id}';

        $this->mockResponse([]);

        $data = $this->client->Events->Event->deleteAnEvent($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    public function testSendRSVP(): void
    {
        $params = [
            'event_id'   => 'string',
            'status'     => 'yes',
            'account_id' => 'string',
        ];

        $this->mockResponse($this->getEventData());

        $data = $this->client->Events->Event->sendRSVP($params);

        $this->assertArrayHasKey('account_id', $data);
    }

    // ------------------------------------------------------------------------------

    public function getProvidData(): array
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

    private function getEventData(): array
    {
        return [
            'account_id'   => '{account_id}',
            'busy'         => true,
            'calendar_id'  => '{calendar_id}',
            'description'  => 'Coffee meeting',
            'ical_uid'     => '{ical_uid}',
            'id'           => '{event_id}',
            'location'     => 'string',
            'message_id'   => 'string',
            'object'       => 'event',
            'owner'        => '<some_email@email.com>',
            'participants' => [
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
