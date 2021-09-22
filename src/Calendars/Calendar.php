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
 * @change 2020/04/26
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
     * get calendars list
     *
     * @param array $params
     *
     * @return array
     */
    public function getCalendarsList(array $params = []): array
    {
        V::doValidate(V::keySet(
            V::keyOptional('view', V::in(['ids', 'count'])),
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
     * add calendar
     *
     * @param array $params
     *
     * @return array
     */
    public function addCalendar(array $params): array
    {
        V::doValidate(V::keySet(...$this->addCalendarRules()), $params);

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->post(API::LIST['calendars']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get calendar
     *
     * @param mixed $calendarId string|string[]
     *
     * @return array
     */
    public function getCalendar(mixed $calendarId): array
    {
        $calendarId = Helper::fooToArray($calendarId);

        V::doValidate(V::simpleArray(V::stringType()->notEmpty()), $calendarId);

        $queues = [];
        $target = API::LIST['oneCalendar'];
        $header = $this->options->getAuthorizationHeader();

        foreach ($calendarId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($header);

            $queues[] = static function () use ($request, $target)
            {
                return $request->get($target);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($calendarId, $pools);
    }
    
    // ------------------------------------------------------------------------------

    /**
     * get free-busy list
     *
     * @param array $params
     *
     * @return array
     */
    public function getFreeBusyList(array $params = []): array
    {
        V::doValidate(V::keySet(
            V::keyOptional('start_time', V::timestampType()),
            V::keyOptional('end_time', V::timestampType()),
            V::keyOptional('emails', V::arrayType()->each(V::stringType()))
        ), $params);

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->post(API::LIST['freeBusy']);
    }

    // ------------------------------------------------------------------------------

    /**
     * rules for add calendar
     *
     * @return array
     */
    private function addCalendarRules(): array
    {
        return
        [
            V::key('name', V::stringType()->notEmpty()),
            V::keyOptional('description', V::stringType()->notEmpty()),
            V::keyOptional('location', V::stringType()->notEmpty()),
            V::keyOptional('timezone', V::in(DateTimeZone::listIdentifiers())),
        ];
    }

    // ------------------------------------------------------------------------------
}
