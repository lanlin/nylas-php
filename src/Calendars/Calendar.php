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
        $accessToken = $this->options->getAccessToken();

        $rule = V::keySet(
            V::keyOptional('view', V::in(['ids', 'count'])),
            V::keyOptional('limit', V::intType()->min(1)),
            V::keyOptional('offset', V::intType()->min(0))
        );

        V::doValidate($rule, $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $header = ['Authorization' => $accessToken];

        return $this->options
            ->getSync()
            ->setQuery($params)
            ->setHeaderParams($header)
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
        $rules = $this->addCalendarRules();

        $accessToken = $this->options->getAccessToken();

        V::doValidate(V::keySet(...$rules), $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $header = ['Authorization' => $accessToken];

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->setHeaderParams($header)
            ->post(API::LIST['calendars']);
    }

    // ------------------------------------------------------------------------------

    /**
     * update calendar
     *
     * @param array $params
     *
     * @return array
     */
    public function updateCalendar(array $params): array
    {
        $rules   = $this->addCalendarRules();
        $rules[] = V::key('id', V::stringType()->notEmpty());

        $accessToken = $this->options->getAccessToken();

        V::doValidate(V::keySet(...$rules), $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $path   = $params['id'];
        $header = ['Authorization' => $accessToken];

        unset($params['id']);

        return $this->options
            ->getSync()
            ->setPath($path)
            ->setFormParams($params)
            ->setHeaderParams($header)
            ->put(API::LIST['oneCalendar']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get calendar
     *
     * @param mixed $calendarId string|string[]
     *
     * @return array
     */
    public function getCalendar($calendarId): array
    {
        $calendarId  = Helper::fooToArray($calendarId);
        $accessToken = $this->options->getAccessToken();

        $rule = V::simpleArray(V::stringType()->notEmpty());

        V::doValidate($rule, $calendarId);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneCalendar'];
        $header = ['Authorization' => $accessToken];

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
     * delete calendar
     *
     * @param mixed $contactId string|string[]
     *
     * @return array
     */
    public function deleteCalendar($contactId): array
    {
        $calendarId  = Helper::fooToArray($contactId);
        $accessToken = $this->options->getAccessToken();

        $rule = V::simpleArray(V::stringType()->notEmpty());

        V::doValidate($rule, $calendarId);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneCalendar'];
        $header = ['Authorization' => $accessToken];

        foreach ($calendarId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($header);

            $queues[] = static function () use ($request, $target)
            {
                return $request->delete($target);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($calendarId, $pools);
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
