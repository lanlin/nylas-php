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
            V::key('end_time', V::timestampType()),
            V::key('start_time', V::timestampType()),
            V::key('emails', V::simpleArray(V::email()))
        ), $params);

        // @todo the nylas docs require to pass Unix timestamp as a string, ball shit!
        $params['end_time']   = (string) $params['end_time'];
        $params['start_time'] = (string) $params['start_time'];

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->post(API::LIST['calendarFreeBusy']);
    }

    // ------------------------------------------------------------------------------
}
