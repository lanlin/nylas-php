<?php

namespace Nylas\Calendars;

use DateTimeZone;
use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Calendar
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/22
 */
class Calendar
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
     * Returns all calendars.
     *
     * @see https://developer.nylas.com/docs/api/#get/calendars
     *
     * @param array $params
     *
     * @return array
     */
    public function returnAllCalendars(array $params = []): array
    {
        V::doValidate(V::keySet(
            V::keyOptional('view', V::in(['ids', 'count', 'expanded'])),
            V::keyOptional('limit', V::intType()->min(1)),
            V::keyOptional('offset', V::intType()->min(0))
        ), $params);

        return $this->options
            ->getSync()
            ->setQuery($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['calendars']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Create a calendar.
     *
     * @see https://developer.nylas.com/docs/api/#post/calendars
     *
     * @param array $params
     *
     * @return array
     */
    public function createACalendar(array $params): array
    {
        V::doValidate(V::keySet(
            V::key('name', V::stringType()->notEmpty()),
            V::keyOptional('timezone', V::in(DateTimeZone::listIdentifiers())),
            V::keyOptional('location', V::stringType()->notEmpty()),
            V::keyOptional('description', V::stringType()->notEmpty()),
        ), $params);

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->post(API::LIST['calendars']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Returns a calendar by ID.
     *
     * @see https://developer.nylas.com/docs/api/#get/calendars/id
     *
     * @param mixed $calendarId string|string[]
     *
     * @return array
     */
    public function returnACalendar(mixed $calendarId): array
    {
        $calendarId = Helper::fooToArray($calendarId);

        V::doValidate(V::simpleArray(V::stringType()->notEmpty()), $calendarId);

        $queues = [];

        foreach ($calendarId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->get(API::LIST['oneCalendar']);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($calendarId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * Updates a calendar.
     *
     * @see https://developer.nylas.com/docs/api/#put/calendars/id
     *
     * @param string $calendarId
     * @param array  $params
     *
     * @return array
     */
    public function updateACalendar(string $calendarId, array $params): array
    {
        V::doValidate(V::keySet(
            V::key('name', V::stringType()->notEmpty()),
            V::keyOptional('timezone', V::in(DateTimeZone::listIdentifiers())),
            V::keyOptional('location', V::stringType()->notEmpty()),
            V::keyOptional('description', V::stringType()->notEmpty()),
        ), $params);

        return $this->options
            ->getSync()
            ->setPath($calendarId)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->put(API::LIST['oneCalendar']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Deletes an existing calendar identified by the specific calendar ID.
     *
     * @see https://developer.nylas.com/docs/api/#delete/calendars/id
     *
     * @param mixed $labelId
     *
     * @return array
     */
    public function deleteACalendar(mixed $calendarId): array
    {
        $calendarId = Helper::fooToArray($calendarId);

        V::doValidate(V::simpleArray(V::stringType()->notEmpty()) , $calendarId);

        $queues = [];

        foreach ($calendarId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->delete(API::LIST['oneCalendar']);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($calendarId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * Check calendar free or busy status.
     *
     * @see https://developer.nylas.com/docs/api/#post/calendars/free-busy
     *
     * @param array $params
     *
     * @return array
     */
    public function calendarFreeOrBusy(array $params = []): array
    {
        V::doValidate(V::keySet(
            V::keyOptional('end_time', V::timestampType()),
            V::keyOptional('start_time', V::timestampType()),
            V::keyOptional('emails', V::simpleArray(V::email()))
        ), $params);

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->post(API::LIST['calendarFreeBusy']);
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
