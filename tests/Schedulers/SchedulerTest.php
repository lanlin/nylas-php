<?php

declare(strict_types = 1);

namespace Schedulers;

use JsonException;
use Tests\AbsCase;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Scheduler Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/24
 *
 * @internal
 */
class SchedulerTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testGetAvailableCalendars(): void
    {
        $id = $this->faker->uuid;

        $this->mockResponse([[
            'id'        => '3aols9hb9fkqtso7zkzcmwgwv',
            'name'      => 'Dorothy Vaughan',
            'email'     => 'demo@nylas.com',
            'calendars' => [[
                'id'        => 'c1gcv6py0xxoksxduu1np13ob',
                'name'      => 'demo@nylas.com',
                'read_only' => false,
            ]],
        ]]);

        $data = $this->client->Schedulers->Scheduler->getAvailableCalendars($id);

        static::assertArrayHasKey('id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testReturnAllSchedulingPages(): void
    {
        $this->mockResponse([$this->getSchedulerBaseData()[0]]);

        $data = $this->client->Schedulers->Scheduler->returnAllSchedulingPages();

        static::assertArrayHasKey('slug', $data[0]);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws JsonException
     */
    public function testReturnASchedulingPage(): void
    {
        $id = $this->faker->uuid;

        $this->mockResponse([$this->getSchedulerBaseData()[0]]);

        $data = $this->client->Schedulers->Scheduler->returnASchedulingPage($id);

        static::assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws JsonException
     */
    public function testDeleteASchedulingPage(): void
    {
        $id = $this->faker->uuid;

        $this->mockResponse(['success' => true]);

        $data = $this->client->Schedulers->Scheduler->deleteASchedulingPage($id);

        static::assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testCreateASchedulingPage(): void
    {
        $params = $this->getSchedulerBaseData()[0];

        $this->mockResponse($this->getSchedulerBaseData()[0]);

        $data = $this->client->Schedulers->Scheduler->createASchedulingPage($params);

        static::assertArrayHasKey('slug', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testUpdateAScheduler(): void
    {
        $id = $this->faker->uuid;

        $params = $this->getSchedulerBaseData()[0];

        $this->mockResponse($this->getSchedulerBaseData()[0]);

        $data = $this->client->Schedulers->Scheduler->updateASchedulingPage($id, $params);

        static::assertArrayHasKey('slug', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return array
     */
    private function getSchedulerBaseData(): array
    {
        return [[
            'access_tokens' => [$this->faker->uuid],
            'config'        => [
                'appearance' => [
                    'color'                    => 'string',
                    'company_name'             => 'string',
                    'logo'                     => 'https://test.com/logo.png',
                    'privacy_policy_redirect'  => 'https://test.com/test',
                    'show_autoschedule'        => false,
                    'show_nylas_branding'      => false,
                    'show_timezone_options'    => false,
                    'show_week_view'           => false,
                    'submit_text'              => 'string',
                    'thank_you_text'           => 'string',
                    'thank_you_redirect'       => 'https://test.com/test',
                    'thank_you_text_secondary' => 'string',
                ],
                'booking' => [
                    'additional_fields' => [
                        [
                            'dropdown_options' => [
                                'Monday',
                                'Tuesday',
                            ],
                            'label'                => 'Your Custom Label',
                            'multi_select_options' => [
                                'Ice Cream',
                                'Spaghetti',
                                'Chips',
                            ],
                            'name'     => 'user_defined_what_is_your_favorite_food_',
                            'order'    => 1,
                            'pattern'  => 'string',
                            'required' => false,
                            'type'     => 'multi-select list',
                        ],
                    ],
                    'additional_guests_hidden'      => false,
                    'available_days_in_future'      => 0,
                    'calendar_invite_to_guests'     => false,
                    'cancellation_policy'           => 'string',
                    'confirmation_emails_to_guests' => false,
                    'confirmation_emails_to_host'   => false,
                    'confirmation_method'           => 'automatic',
                    'interval_minutes'              => 0,
                    'min_booking_notice'            => 0,
                    'min_buffer'                    => 0,
                    'min_cancellation_notice'       => 0,
                    'name_field_hidden'             => false,
                    'opening_hours'                 => [
                        [
                            'account_id' => '3aols9hb9fkqtso7zkzcmwgwv',
                            'days'       => [
                                'M',
                                'W',
                                'F',
                            ],
                            'end'   => '17:00',
                            'start' => '09:00',
                        ],
                    ],
                    'scheduling_method' => 'round-robin-maximize-fairness',
                ],
                'calendar_ids' => [
                    'REPLACE-WITH-ACCOUNT-ID' => [
                        'availability' => [
                            'cwrnjyl28m1hdg9s8c384ldqk',
                            '4ym6cirul3v0ydku9mvbp4ip1',
                        ],
                        'booking' => '4ym6cirul3v0ydku9mvbp4ip1',
                    ],
                ],
                'event' => [
                    'capacity'     => 10,
                    'duration'     => 30,
                    'location'     => 'Coffee Shop',
                    'participants' => [
                        [
                            'email' => 'kat@spacetech.com',
                            'name'  => 'Katherine Johnson',
                        ],
                    ],
                    'title'          => '30-min Coffee Meeting',
                    'template_title' => '$[duration]-min Coffee Meeting with $[invitee]',
                ],
                'expire_after' => [
                    'date' => 1633446000,
                    'uses' => 5,
                ],
                'disable_emails'    => false,
                'locale'            => 'en',
                'locale_for_guests' => 'fr',
                'reminders'         => [
                    [
                        'delivery_method'    => 'email',
                        'delivery_recipient' => 'customer',
                        'email_subject'      => 'Your meeting is coming up!',
                        'time_before_event'  => 30,
                        'webhook_url'        => 'https://your-webhook-url.com',
                    ],
                ],
                'timezone' => 'America/Chicago',
            ],
            'name' => '30-min Coffee Meeting',
            'slug' => 'dorothy-vaughan-30min',
        ]];
    }

    // ------------------------------------------------------------------------------
}
