<?php

namespace Nylas\Events;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Events
 * ----------------------------------------------------------------------------------
 *
 * @see https://docs.nylas.com/reference#event-limitations
 *
 * @author lanlin
 * @change 2021/09/22
 */
class Event
{
    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Event constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * get events list
     *
     * @param array $params
     *
     * @return array
     */
    public function getEventsList(array $params = []): array
    {
        V::doValidate(V::keySet(
            V::keyOptional('view', V::in(['ids', 'count'])),
            ...$this->getBaseRules()
        ), $params);

        return $this->options
            ->getSync()
            ->setQuery($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['events']);
    }

    // ------------------------------------------------------------------------------

    /**
     * add event
     *
     * @param array $params
     *
     * @return array
     */
    public function addEvent(array $params): array
    {
        V::doValidate($this->addEventRules(), $params);

        $notify = 'notify_participants';
        $query  = isset($params[$notify]) ? [$notify => $params[$notify]] : [];

        unset($params['notify_participants']);

        return $this->options
            ->getSync()
            ->setQuery($query)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->post(API::LIST['events']);
    }

    // ------------------------------------------------------------------------------

    /**
     * update event
     *
     * @param array $params
     *
     * @return array
     */
    public function updateEvent(array $params): array
    {
        V::doValidate($this->updateEventRules(), $params);

        $path   = $params['id'];
        $notify = 'notify_participants';
        $query  = isset($params[$notify]) ? [$notify => $params[$notify]] : [];

        unset($params['id'], $params['notify_participants']);

        return $this->options
            ->getSync()
            ->setPath($path)
            ->setQuery($query)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->put(API::LIST['oneEvent']);
    }

    // ------------------------------------------------------------------------------

    /**
     * rsvping
     *
     * @param array $params
     *
     * @return mixed
     */
    public function rsvping(array $params)
    {
        if (empty($params['account_id']))
        {
            $params['account_id'] = $this->options->getAccountId();
        }

        V::doValidate(V::keySet(
            V::key('status', V::in(['yes', 'no', 'maybe'])),
            V::key('event_id', V::stringType()->notEmpty()),
            V::key('account_id', V::stringType()->notEmpty()),
            V::keyOptional('notify_participants', V::boolType())
        ), $params);

        $notify = 'notify_participants';
        $query  = isset($params[$notify]) ? [$notify => $params[$notify]] : [];

        unset($params['notify_participants']);

        return $this->options
            ->getSync()
            ->setQuery($query)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->post(API::LIST['oneEvent']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get event
     *
     * @param array $params
     *
     * @return array
     */
    public function getEvent(array $params): array
    {
        $params = Helper::arrayToMulti($params);

        V::doValidate(V::simpleArray(V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            ...$this->getBaseRules(),
        )), $params);

        $queues = [];

        foreach ($params as $item)
        {
            $id = $item['id'];
            unset($item['id']);

            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setFormParams($item)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->get(API::LIST['oneEvent']);
            };
        }

        $evtID = Helper::generateArray($params, 'id');
        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($evtID, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * delete event
     *
     * @param array $params
     *
     * @return array
     */
    public function deleteEvent(array $params): array
    {
        $params = Helper::arrayToMulti($params);

        V::doValidate(V::simpleArray(V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::keyOptional('notify_participants', V::boolType())
        )), $params);

        $queues = [];
        $notify = 'notify_participants';

        foreach ($params as $item)
        {
            $query = isset($item[$notify]) ? [$notify => $item[$notify]] : [];

            $request = $this->options
                ->getAsync()
                ->setPath($item['id'])
                ->setQuery($query)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->delete(API::LIST['oneEvent']);
            };
        }

        $evtID = Helper::generateArray($params, 'id');
        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($evtID, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * rules for add event
     *
     * @return \Nylas\Utilities\Validator
     */
    private function addEventRules(): V
    {
        return V::keySet(
            V::key('when', $this->timeRules()),
            ...$this->getEventBaseRules(),
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * rules for update event
     *
     * @return \Nylas\Utilities\Validator
     */
    private function updateEventRules(): V
    {
        return V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::keyOptional('when', $this->timeRules()),
            ...$this->getEventBaseRules(),
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * event base validate rules
     *
     * @return array
     */
    private function getBaseRules(): array
    {
        return
        [
            V::keyOptional('limit', V::intType()->min(1)),
            V::keyOptional('offset', V::intType()->min(0)),
            V::keyOptional('event_id', V::stringType()->notEmpty()),
            V::keyOptional('calendar_id', V::stringType()->notEmpty()),

            V::keyOptional('title', V::stringType()->notEmpty()),
            V::keyOptional('location', V::stringType()->notEmpty()),
            V::keyOptional('description', V::stringType()->notEmpty()),
            V::keyOptional('show_cancelled', V::boolType()),
            V::keyOptional('expand_recurring', V::boolType()),

            V::keyOptional('ends_after', V::timestampType()),
            V::keyOptional('ends_before', V::timestampType()),
            V::keyOptional('starts_after', V::timestampType()),
            V::keyOptional('starts_before', V::timestampType()),
        ];
    }

    // ------------------------------------------------------------------------------

    /**
     * get event base rules
     *
     * @return array
     */
    private function getEventBaseRules(): array
    {
        $recurrenceRule = V::keySet(
            V::key('rrule', V::simpleArray()),
            V::key('timezone', V::stringType()),
        );

        $participantsRule = V::simpleArray(V::keySet(
            V::key('email', V::email()),
            V::keyOptional('name', V::stringType()),
            V::keyOptional('status', V::in(['yes', 'no', 'maybe', 'noreply'])),
            V::keyOptional('comment', V::stringType())
        ));

        return
        [
            V::key('calendar_id', V::stringType()->notEmpty()),
            V::keyOptional('busy', V::boolType()),
            V::keyOptional('read_only', V::boolType()),
            V::keyOptional('title', V::stringType()->notEmpty()),
            V::keyOptional('location', V::stringType()->notEmpty()),
            V::keyOptional('recurrence', $recurrenceRule),
            V::keyOptional('description', V::stringType()->notEmpty()),
            V::keyOptional('participants', $participantsRule),
            V::keyOptional('conferencing', $this->conferenceRules()),
            V::keyOptional('notify_participants', V::boolType()),
        ];
    }

    // ------------------------------------------------------------------------------

    /**
     * get event time rules
     *
     * @return \Nylas\Utilities\Validator
     */
    private function timeRules(): V
    {
        return V::anyOf(
            // time
            V::keySet(V::key('time', V::timestampType())),

            // date
            V::keySet(V::key('date', V::date('Y-m-d'))),

            // timespan
            V::keySet(
                V::key('end_time', V::timestampType()),
                V::key('start_time', V::timestampType())
            ),

            // date span
            V::keySet(
                V::key('end_date', V::date('Y-m-d')),
                V::key('start_date', V::date('Y-m-d'))
            )
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * get event conference rules
     *
     * @return \Nylas\Utilities\Validator
     */
    private function conferenceRules(): V
    {
        $webEx = V::keySet(
            V::key('provider', V::equals('WebEx')),
            V::key('details', V::keySet(
                V::key('password', V::stringType()),
                V::key('pin', V::stringType()),
                V::key('url', V::stringType())
            ))
        );

        $zoomMeeting = V::keySet(
            V::key('provider', V::equals('Zoom Meeting')),
            V::key('details', V::keySet(
                V::key('meeting_code', V::stringType()),
                V::key('password', V::stringType()),
                V::key('url', V::stringType()),
            ))
        );

        $goToMeeting = V::keySet(
            V::key('provider', V::equals('GoToMeeting')),
            V::key('details', V::keySet(
                V::key('meeting_code', V::stringType()),
                V::key('phone', V::simpleArray()),
                V::key('url', V::stringType()),
            ))
        );

        $googleMeet = V::keySet(
            V::key('provider', V::equals('Google Meet')),
            V::key('details', V::keySet(
                V::key('phone', V::simpleArray()),
                V::key('pin', V::stringType()),
                V::key('url', V::stringType()),
            ))
        );

        return V::oneOf($webEx, $zoomMeeting, $goToMeeting, $googleMeet);
    }

    // ------------------------------------------------------------------------------
}
