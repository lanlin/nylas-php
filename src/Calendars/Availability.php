<?php

declare(strict_types = 1);

namespace Nylas\Calendars;

use DateTimeZone;
use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Calendar
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 */
class Availability
{
    // ------------------------------------------------------------------------------

    /**
     * @var Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Calendar constructor.
     *
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * Check multiple calendars to find available time slots for a single meeting.
     * It checks the provider's primary calendar.
     *
     * @see https://developer.nylas.com/docs/api/v2/#post-/calendars/availability
     *
     * @param array $params
     *
     * @return array
     * @throws GuzzleException
     */
    public function availabilityForASingleMeeting(array $params = []): array
    {
        V::doValidate($this->getMeetingRules(true), $params);

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->post(API::LIST['calendarAbility']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Check to find availability for multiple meetings with several participants.
     * Use this endpoint to build itineraries where participants with the same availability are combined.
     * It checks the provider's primary calendar.
     *
     * @see https://developer.nylas.com/docs/api/v2/#post-/calendars/availability/consecutive
     *
     * @param array $params
     *
     * @return array
     * @throws GuzzleException
     */
    public function availabilityForMultipleMeetings(array $params = []): array
    {
        V::doValidate($this->getMeetingRules(false), $params);

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->post(API::LIST['calendarConsecutive']);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param bool $single
     *
     * @return V
     */
    private function getMeetingRules(bool $single): V
    {
        $emailsRules = match ($single)
        {
            true  => V::simpleArray(V::email()),
            false => V::simpleArray(V::simpleArray(V::email())),
        };

        $timeSlot = V::keySet(
            V::key('object', V::stringType()::notEmpty()),
            V::key('status', V::stringType()::notEmpty()),
            V::key('end_time', V::timestampType()),
            V::key('start_time', V::timestampType()),
        );

        $freeBusy = V::keySet(
            V::key('email', V::email()),
            V::key('object', V::stringType()::notEmpty()),
            V::key('time_slots', V::simpleArray($timeSlot)),
        );

        $openHours = V::keySet(
            V::key('end', V::time('H:i')),
            V::key('start', V::time('H:i')),
            V::key('days', V::simpleArray(V::in(['0', '1', '2', '3', '4', '5', '6']))),
            V::key('emails', $emailsRules),
            V::key('timezone', V::in(DateTimeZone::listIdentifiers())),
            V::key('object_type', V::equals('open_hours')),
        );

        $calendars = V::keySet(
            V::key('account_id', V::stringType()),
            V::key('calendar_ids', V::simpleArray(V::stringType())),
        );

        return V::keySet(
            V::key('emails', $emailsRules),
            V::key('end_time', V::timestampType()),
            V::key('start_time', V::timestampType()),
            V::key('free_busy', V::simpleArray($freeBusy)),
            V::key('interval_minutes', V::intType()),
            V::key('duration_minutes', V::intType()),
            V::keyOptional('round_robin', V::stringType()),
            V::keyOptional('buffer', V::intType()),
            V::keyOptional('open_hours', V::simpleArray($openHours)),
            V::keyOptional('calendars', V::simpleArray($calendars)),
        );
    }

    // ------------------------------------------------------------------------------
}
