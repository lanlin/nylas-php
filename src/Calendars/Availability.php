<?php

namespace Nylas\Calendars;

use DateTimeZone;
use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Calendar
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/24
 */
class Availability
{
    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Calendar constructor.
     *
     * @param \Nylas\Utilities\Options $options
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
     * @see https://developer.nylas.com/docs/api/#post/calendars/availability
     *
     * @param array $params
     *
     * @return array
     */
    public function availabilityForASingleMeeting(array $params = []): array
    {
        V::doValidate($this->getMeetingRules(), $params);

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
     * @see https://developer.nylas.com/docs/api/#post/calendars/availability/consecutive
     *
     * @param array $params
     *
     * @return array
     */
    public function availabilityForMultipleMeetings(array $params = []): array
    {
        V::doValidate($this->getMeetingRules(), $params);

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->post(API::LIST['calendarConsecutive']);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return \Nylas\Utilities\Validator
     */
    private function getMeetingRules(): V
    {
        $timeSlot = V::keySet(
            V::key('object', V::stringType()->notEmpty()),
            V::key('status', V::stringType()->notEmpty()),
            V::key('end_time', V::timestampType()),
            V::key('start_time', V::timestampType()),
        );

        $freeBusy = V::keySet(
            V::key('object', V::stringType()->notEmpty()),
            V::key('email', V::email()),
            V::key('time_slot', V::simpleArray($timeSlot)),
        );

        $openHours = V::keySet(
            V::key('end', V::anyOf(V::equals(0), V::time('H:i'))),
            V::key('start', V::anyOf(V::equals(0), V::time('H:i'))),
            V::key('days', V::simpleArray(V::in(['0', '1', '2', '3', '4', '5', '6']))),
            V::key('emails', V::simpleArray(V::email())),
            V::key('timezone', V::in(DateTimeZone::listIdentifiers())),
            V::key('object_type', V::equals('open_hours')),
        );

        return V::keySet(
            V::key('emails', V::simpleArray(V::email())),
            V::key('end_time', V::timestampType()),
            V::key('start_time', V::timestampType()),
            V::key('free_busy', V::simpleArray($freeBusy)),
            V::key('open_hours', V::simpleArray($openHours), false),
            V::key('interval_minutes', V::intType()),
            V::key('duration_minutes', V::intType()),
        );
    }

    // ------------------------------------------------------------------------------
}
