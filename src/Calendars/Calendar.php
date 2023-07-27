<?php

declare(strict_types = 1);

namespace Nylas\Calendars;

use function count;
use function is_array;
use function array_keys;
use function array_values;

use DateTimeZone;
use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
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
class Calendar
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
     * Returns all calendars.
     *
     * @see https://developer.nylas.com/docs/api/v2/#get-/calendars
     *
     * @param array $params
     *
     * @return array
     * @throws GuzzleException
     */
    public function returnAllCalendars(array $params = []): array
    {
        V::doValidate(V::keySet(
            V::keyOptional('view', V::in(['ids', 'count', 'expanded'])),
            V::keyOptional('limit', V::intType()::min(1)),
            V::keyOptional('offset', V::intType()::min(0)),

            // @see https://developer.nylas.com/docs/api/metadata/#keep-in-mind
            V::keyOptional('metadata_key', V::stringType()::length(1, 40)),
            V::keyOptional('metadata_value', V::stringType()::length(1, 500)),
            V::keyOptional('metadata_paire', V::stringType()::length(3, 27100)),
            V::keyOptional('metadata_search', V::stringType()::notEmpty())
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
     * @see https://developer.nylas.com/docs/api/v2/#post-/calendars
     *
     * @param array $params
     *
     * @return array
     * @throws GuzzleException
     */
    public function createACalendar(array $params): array
    {
        V::doValidate(V::keySet(
            V::key('name', V::stringType()::notEmpty()),
            V::keyOptional('timezone', V::in(DateTimeZone::listIdentifiers())),
            V::keyOptional('location', V::stringType()::notEmpty()),
            V::keyOptional('metadata', self::metadataRules()),
            V::keyOptional('description', V::stringType()::notEmpty()),
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
     * @see https://developer.nylas.com/docs/api/v2/#get-/calendars/id
     *
     * @param mixed $calendarId string|string[]
     *
     * @return array
     */
    public function returnACalendar(mixed $calendarId): array
    {
        $calendarId = Helper::fooToArray($calendarId);

        V::doValidate(V::simpleArray(V::stringType()::notEmpty()), $calendarId);

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
     * @see https://developer.nylas.com/docs/api/v2/#put-/calendars/id
     *
     * @param string $calendarId
     * @param array  $params
     *
     * @return array
     * @throws GuzzleException
     */
    public function updateACalendar(string $calendarId, array $params): array
    {
        V::doValidate(V::keySet(
            V::key('name', V::stringType()::notEmpty()),
            V::keyOptional('timezone', V::in(DateTimeZone::listIdentifiers())),
            V::keyOptional('location', V::stringType()::notEmpty()),
            V::keyOptional('metadata', self::metadataRules()),
            V::keyOptional('description', V::stringType()::notEmpty()),
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
     * @see https://developer.nylas.com/docs/api/v2/#delete-/calendars/id
     *
     * @param mixed $calendarId
     *
     * @return array
     */
    public function deleteACalendar(mixed $calendarId): array
    {
        $calendarId = Helper::fooToArray($calendarId);

        V::doValidate(V::simpleArray(V::stringType()::notEmpty()), $calendarId);

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
     * @see https://developer.nylas.com/docs/api/v2/#post-/calendars/free-busy
     *
     * @param array $params
     *
     * @return array
     * @throws GuzzleException
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

    /**
     * get metadata array rules
     *
     * @see https://developer.nylas.com/docs/api/metadata/#keep-in-mind
     *
     * @return V
     */
    private static function metadataRules(): V
    {
        return V::callback(static function (mixed $input): bool
        {
            if (!is_array($input) || count($input) > 50)
            {
                return false;
            }

            $keys = array_keys($input);
            $isOk = V::each(V::stringType()::length(1, 40))->validate($keys);

            if (!$isOk)
            {
                return false;
            }

            // https://developer.nylas.com/docs/api/metadata/#delete-metadata
            return V::each(V::stringType()::length(0, 500))->validate(array_values($input));
        });
    }

    // ------------------------------------------------------------------------------
}
